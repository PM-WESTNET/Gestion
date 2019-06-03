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

}