<?php

namespace app\modules\sale\models\search;

use app\modules\sale\models\ProductToInvoice;
use Yii;
use yii\db\Query;

/**
 * ProductToInvoiceSearch represents the model behind the search form about `app\modules\sale\modules\contract\models\ProductToInvoice`.
 */
class ProductToInvoiceSearch extends ProductToInvoice
{

    /**
     * Retornoa las cuotas pendientes
     *
     * @param array $params
     *
     * @return Query
     */
    public function search($periods, $contract_id = null, $customer_id = null )
    {
        $finalPeriods = [];
        if (is_array($periods)) {
            foreach ($periods as $period) {
                if (!$period instanceof \DateTime) {
                    $period = new \DateTime($period);
                }
                $finalPeriods[] = $period;
            }
        }
        $query = ProductToInvoice::find();
        $query->from('product_to_invoice');
        $query->leftJoin('contract_detail', 'product_to_invoice.contract_detail_id = contract_detail.contract_detail_id');

        $where = ['and', "product_to_invoice.status='active'"];

        $where2[] = 'or';
        $params = [];
        if ($contract_id) {
            $where2[] = 'contract_detail.contract_id=:contract_id';
            $params[':contract_id'] = $contract_id;
        }
        if ($customer_id) {
            $where2[] = 'product_to_invoice.customer_id=:customer_id';
            $params[':customer_id'] = $customer_id;
        }

        $aPeriods[] = 'or';
        foreach($finalPeriods as $period) {
            $aPeriods[] = "date_format(product_to_invoice.period, '%Y%m') = '" . $period->format('Ym') ."'";
        }
        $where[] = $where2;
        $where[] = $aPeriods;
        $query->where($where, $params);
        return $query;
    }

    /**
     * Retorna los productos a facturar filtrados por customer.
     *
     * @param $customer_id
     * @return \yii\db\ActiveQuery
     */
    public function searchByCustomer($customer_id)
    {
        $query = ProductToInvoice::find();
        $query
            ->leftJoin('contract_detail', 'product_to_invoice.contract_detail_id = contract_detail.contract_detail_id')
            ->leftJoin('contract', 'contract_detail.contract_id = contract.contract_id')
            ->where(['contract.customer_id'=>$customer_id])
            ->orWhere(['product_to_invoice.customer_id'=>$customer_id])
            ->orderBy(['product_to_invoice.period'=>SORT_ASC, 'product_to_invoice.status'=>SORT_DESC]);

        return $query;
    }
}
