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
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\models\Discount;
use app\modules\sale\models\search\CustomerSearch;
use Yii;

class ReferencedDiscount
{

    /**
     * Aplico el ultimo descunto disponible para recomendados.
     *
     * @param Payment $payment
     * @throws \Exception
     */
    public function applyDiscount(Payment $payment)
    {
        try {
            $discounts = Discount::find()
                ->where(['referenced'=>1, 'status' => Discount::STATUS_ENABLED])
                ->orderBy(['discount_id'=>SORT_DESC])->all();

            if(count($discounts)>0) {
                Yii::debug($discounts);
                /** @var Discount $discount */
                foreach ($discounts as $discount) {
                    Yii::debug($discount);
                    if ($this->firstInvoicePayed($payment->customer_id) &&
                        !$this->customerHasDiscount($payment->customer_id, $discount->discount_id) ) {
                        $chd = new CustomerHasDiscount();
                        $chd->customer_id = $payment->customer->customer_reference_id;
                        $chd->discount_id = $discount->discount_id;
                        $chd->status = 'enabled';
                        $chd->from_date   = (new \DateTime('first day of next month'))->format('d-m-Y');
                        $chd->to_date     = (new \DateTime('last day of next month'))->format('d-m-Y');
                        $chd->save();
                        break;
                    }
                }
            } else {
                throw new \Exception(Yii::t('westnet', 'The discount for referenced is not configurated.'));
            }
        } catch (\Exception $ex) {
            error_log(print_r($ex,1));
        }

    }

    /**
     * Retorno si la primer factura ha sido pagada
     *
     * @param $customer_id
     * @return bool
     */
    private function firstInvoicePayed($customer_id)
    {
        $search = new CustomerSearch();
        $result = $search->searchDebtBills($customer_id);
        return $result['debt_bills'] == 0 && $result['payed_bills'] == 1;
    }

    private function customerHasDiscount($customer_id, $discount_id)
    {
        return (CustomerHasDiscount::find()
                ->where(['customer_id'=>$customer_id, 'discount_id'=> $discount_id])->count())!=0;
    }
}
