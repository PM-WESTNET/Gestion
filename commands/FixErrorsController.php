<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/02/19
 * Time: 16:38
 */
namespace app\commands;

use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\components\BillExpert;
use app\modules\sale\models\Bill;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Customer;
use app\modules\sale\models\PointOfSale;
use app\modules\sale\models\TaxCondition;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\components\SecureConnectionUpdate;
use yii\db\Query;

class FixErrorsController extends \yii\console\Controller
{

    public $billId = [];
    public $providerBillsIds = [];

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'billIds',
            'providerBillsIds'
        ]);
    }

    /**
     * Actualiza los comprobantes del periodo indicado para que el valor neto de la factura incluya los valores de los
     * impuestos que tienen como porcentaje 0.
     * Esto se hace porque en los libros de iva se suman los impuestos de iva al 21 conjuntamente con lo que se declara en conceptos no gravados.
     */
    public function actionUpdateNetInProviderBills($date_from = null, $date_to = null)
    {
        if(!$date_from) {
            $date_from = '2019-01-01';
            $date_to = (new \DateTime($date_from))->modify('last day of this month')->format('Y-m-d');
        }

       $provider_bills = ProviderBill::find()->where(['between','date', $date_from, $date_to])->all();

        foreach ($provider_bills as $provider_bill) {
            $provider_bill->calculateTotal();
        }
    }

    /**
     * Actualiza el neto en tax rate de los comprobantes a proveedor, y actualiza los totales de los mismos, solo a los tax rate que tienen porcentaje (IVA)
     * Para llamar a esta acción ./yii set-tax-rate-net-into-provider-bills 1006215,1006135 por ejemplo
     */
    public function actionSetTaxRateNetIntoProviderBills(array $providerBillsIds) {
        foreach ($providerBillsIds as $providerBillId) {
            $provider_bill = ProviderBill::findOne($providerBillId);

            if(!$provider_bill) {
                echo "Problemas para encontrar el comprobante con id $providerBillId\n";
            } else {
                echo "Actualizando comprobante $provider_bill->provider_bill_id ...\n";
                $provider_bill_has_tax_rate = $provider_bill->getProviderBillHasTaxRates()->where(['in','tax_rate_id', [1,2,3,4,14,15]])->all();

                foreach ($provider_bill_has_tax_rate as $pbhtr) {
                    $new_net = $pbhtr->amount / $pbhtr->taxRate->pct;
                    $pbhtr->updateAttributes(['net' => round($new_net,2)]);
                }

                $provider_bill->calculateTotal();
            }
        }
    }

    /**
     * Vuelve a calcular los valores de total, taxes, y amount del comprobante dados y lo actualiza (bill_id).
     * Para llamar a esta acción ./yii fix-errors/update-amounts-from-bills 1006215,1006135 por ejemplo
     * @param array $bill_ids
     */
    public function actionUpdateAmountsFromBills(array $billIds) {
        foreach ($billIds as $id) {
            $bill = Bill::findOne($id);
            if(!$bill){
                echo "Problemas para encontrar el comprobante con id $id\n";
            }
            $bill->updateAmounts();
            echo "Comprobante con id $id actualizado\n";
        }
    }

    /**
     * @param array $billIds
     * @throws \Exception
     * Actualiza la fecha de los comprobantes dados al dia de hoy y borra el numero del mismo.
     * No permite realizarlo si el comprobante no esta en borrador
     */
    public function actionUpdateDateAndEraseNumberFromBill(array $billIds) {
        $date = (new \DateTime('now'))->format('Y-m-d');
        foreach ($billIds as $id) {
            $bill = Bill::findOne($id);
            if(!$bill){
                echo "Problemas para encontrar el comprobante con id $id\n";
            } else {
                if($bill->status != 'draft') {
                    echo "El comprobante no puede ser actualizado si no se encuentra en estado borrador id $id\n";
                } else {
                    $bill->updateAttributes(['date' => $date, 'number' => '']);
                    echo "Fecha y numero actualizados del comprobante id $id\n";
                }
            }
        }
    }

    /**
     *
     * Corrige los valores de debe y haber de una conciliacion, invirtiendo dichos valores en el resumen y corrgiendo los
     * movimientos generados por la conciliacion
     * @param $name
     * @return bool
     */
    public function actionFixConciliation($name) {

        $trasaction = \Yii::$app->db->beginTransaction();

        try {

            $conciliation = \app\modules\accounting\models\Conciliation::findOne(['name' => $name]);

            if ($conciliation) {
                $resume = $conciliation->resume;

                foreach ($resume->resumeItems as $resumeItem) {

                    if ($resumeItem->debit > 0) {
                        $resumeItem->credit = $resumeItem->debit;
                        $resumeItem->debit = 0;
                        $resumeItem->updateAttributes(['debit', 'credit']);
                    }else {
                        $resumeItem->debit = $resumeItem->credit;
                        $resumeItem->credit = 0;
                        $resumeItem->updateAttributes(['debit', 'credit']);
                    }

                }

                $conciliationItems = $conciliation->conciliationItems;

                foreach ($conciliationItems as $conciliationItem) {
                    $amount = 0;
                    if (strpos($conciliationItem->description, 'Conciliacion de') === false) {
                        $movements = $conciliationItem->accountMovementItems;

                        foreach ($movements as $movement) {

                            if ($movement->debit > 0) {
                                $movement->credit = $movement->debit;
                                $movement->debit = 0;
                                $movement->updateAttributes(['credit', 'debit']);
                            }else {
                                $movement->debit = $movement->credit;
                                $movement->credit = 0;
                                $movement->updateAttributes(['credit', 'debit']);
                            }
                        }
                    }

                    foreach ($conciliationItem->resumeItems as $resumeItem) {
                        $amount += $resumeItem->debit - $resumeItem->credit;
                    }

                    $conciliationItem->updateAttributes(['amount' => $amount]);

                }
            }
            $trasaction->commit();
        } catch (\Exception $exception) {
            $trasaction->rollBack();
            return false;
        }
    }


    /**
     * Actualiza el contrato pasado en el isp
     */
    public function actionUpdateContract($contract_id) {

        $contract = Contract::findOne($contract_id);

        if (empty($contract)) {
            echo 'Contrato no encontrado';
            echo "\n";
            return false;
        }

        if (empty($contract->connection)) {
            echo 'Conexion no encontrada';
            echo "\n";
            return false;
        }

        $scu = new SecureConnectionUpdate();

        $scu->update($contract->connection, $contract);



    }
}