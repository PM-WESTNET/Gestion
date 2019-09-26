<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 8/11/16
 * Time: 13:15
 */

namespace app\modules\westnet\components;

use app\modules\checkout\models\Payment;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\models\Discount;
use app\modules\sale\models\search\CustomerSearch;
use Yii;

class ReferencedDiscount
{

    /**
     * Aplico el ultimo descunto disponible para recomendados.
     * Se aplica cuando el cliente que ha referenciado ha pagado la primera factura y el cliente referenciado no tiene aplicado el descuento.
     *
     * @param Payment $payment
     * @throws \Exception
     */
    public function applyDiscount(Payment $payment)
    {
        //Sólo se aplica el descuento a clientes que tengan un cliente referenciado.
        if($payment->customer->customer_reference_id) {

            //En Customerhasdicount hacer que no tenga fecha y que tenga otro campo que indique q no vence, sino que se intenta aplicar siempre,
            //hasta que realmente se aplique y se marque como aplicado.

            $discounts = Discount::find()
                ->where(['referenced' => 1, 'status' => Discount::STATUS_ENABLED])
                ->orderBy(['discount_id' => SORT_DESC])->all();

            foreach ($discounts as $discount) {
                //Verifico que la primera factura esté pagada y que no tenga un descuento
                if(Customer::hasFirstBillPayed($payment->customer_id) && !$this->customerHasDiscount($payment->customer->customer_reference_id, $discount->discount_id)) {

                    $chd = new CustomerHasDiscount([
                        'customer_id' => $payment->customer->customer_reference_id,
                        'discount_id' => $discount->discount_id,
                        'status' => CustomerHasDiscount::STATUS_ENABLED,
                        'from_date' => (new \DateTime('first day of next month'))->format('d-m-Y')
                    ]);

                    //Si el descuento es "persistente", no debo indicar fechas.
                    if(!$discount->persistent) {
                        $chd->to_date = (new \DateTime('last day of next month'))->format('d-m-Y');
                    }
                    $chd->save();
                }
            }
        }
    }

    private function customerHasDiscount($customer_id, $discount_id)
    {
        return (CustomerHasDiscount::find()
                ->where(['customer_id'=>$customer_id, 'discount_id'=> $discount_id])->count())!=0;
    }
}
