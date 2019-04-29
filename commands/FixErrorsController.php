<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/02/19
 * Time: 16:38
 */
namespace app\commands;

use app\modules\provider\models\ProviderBill;

class FixErrorsController extends \yii\console\Controller
{
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
}