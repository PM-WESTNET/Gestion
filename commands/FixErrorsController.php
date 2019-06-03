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
use yii\db\Query;

class FixErrorsController extends \yii\console\Controller
{

    public $billId = [];

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'billIds'
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

    public function actionFixDoubleBills($company_id, $date, $limit = null)
    {

        echo "Iniciando ....";
        echo "\n";

        $customersDuplicatedBillsQuery= (new Query())
            ->select(['customer_id'])
            ->from('bill')
            ->andWhere(['date' => \Yii::$app->formatter->asDate($date, 'yyyy-MM-dd')])
            ->andWhere(['<>','total', 0])
            ->groupBy(['customer_id'])
            ->having(['>', 'count(*)', 1])
            ->limit($limit);

        $customersDuplicatedBills = $customersDuplicatedBillsQuery->all();

        echo "Total de clientes con facturas duplicadas: " . $customersDuplicatedBillsQuery->count();
        echo "\n";

        $customers_id = array_map(function($customer){ return $customer['customer_id'];}, $customersDuplicatedBills);

        $customers = Customer::find()
            ->andWhere(['IN', 'customer_id', $customers_id])
            ->andWhere(['company_id' => $company_id])
            ->all();

        $point_of_sale = PointOfSale::findOne(['company_id' => $company_id, 'default' => 1]);

        if (empty($point_of_sale)) {
            echo "No se encuentra Point of Sale";
            echo "\n";
            return false;
        }

        foreach ($customers as $customer) {
            $taxIvaInscr = TaxCondition::findOne(['name' => 'IVA Inscripto']);

            if ($customer->tax_condition_id === $taxIvaInscr->tax_condition_id) {
                $bill_type = BillType::findOne(['name' => 'Nota Crédito A']);
            }else {
                $bill_type = BillType::findOne(['name' => 'Nota Crédito B']);
            }

            $bills = $customer->getBills()->orderBy(['bill.timestamp' => SORT_DESC])->all();

            if (!empty($bills) && $bills[0]->total === $bills[1]->total) {
                $this->createBill($bill_type->bill_type_id, $point_of_sale->point_of_sale_id,$customer, $bills[0]->amount, $bills[0]->total);
            }
        }

        return true;
    }

    public function createBill($bill_type_id, $point_of_sale_id, $customer, $net, $total)
    {
        $default_unit_id = Config::getValue('default_unit_id');
        echo "Creando Bill para " . $customer->fullName. ' por $'. $total;
        echo "\n";
        $bill = BillExpert::createBill($bill_type_id);
        $bill->company_id = $customer->company->company_id;
        $bill->point_of_sale_id = $point_of_sale_id;
        $bill->customer_id = $customer->customer_id;
        $bill->status = 'draft';

        $bill->save(false);
        echo "Cualquier cosa";
        echo print_r($bill->getErrors(), 1);

        $a = $bill->addDetail([
            'product_id' => null,
            'unit_id' => $default_unit_id,
            'qty' => 1,
            'type' => null,
            'unit_net_price' => abs($net),
            'unit_final_price' => abs($total),
            'concept' => 'Corrección error de Facturación',
            'discount_id' => null,
            'unit_net_discount' => null,
        ]);



        $bill->fillNumber();

        $bill->close();

        return $bill;
    }

    private function getBillNumber($bill_type_id, $company_id)
    {
        $lastNumber = Bill::find()->where([
                'bill_type_id' => $bill_type_id,
                'status' => 'closed',
                'company_id' => $company_id])
                ->max('number') + 1;

        return $lastNumber;
    }

}