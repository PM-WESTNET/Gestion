<?php

namespace app\modules\sale\modules\contract\models\search;

use app\modules\sale\models\ProductPrice;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\modules\contract\models\ContractDetail;
use yii\db\Query;

/**
 * ContractDetailSearch represents the model behind the search form about `app\modules\sale\models\ContractDetail`.
 */
class ContractDetailSearch extends ContractDetail
{
    public $product_name;
    public $qty_payments;
    public $amount_payment;
    public $total;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_id', 'product_id', 'to_date'], 'integer'],
            [['status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ContractDetail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'contract_id' => $this->contract_id,
            'product_id' => $this->product_id,
            'to_date' => $this->to_date,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
    
    
    public static function getdataProviderDetail($contract_id = null){
                    
           $dataProvider = new ActiveDataProvider([
            'query' => ContractDetail::find()->where(['contract_id'=>$contract_id])->orderBy(['from_date' => SORT_DESC]),
            'pagination' => [
            'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function searchLogs($params)
    {

        $leftProductPrice = ProductPrice::find()
            ->select(['product_price_id', 'product_id', 'max(timestamp)'])
            ->groupBy(['product_id']);

        $details = ContractDetail::find()
            ->select(['contract_detail.contract_detail_id', '(0) as contract_detail_log_id', 'p.type', 'p.name AS product_name',
                'contract_detail.date', 'contract_detail.from_date', 'contract_detail.to_date', 'contract_detail.status',
                'fp.qty_payments', 'fp.amount_payment',
                'IF(fp.funding_plan_id IS NULL, truncate(net_price + taxes, 2), (fp.qty_payments * fp.amount_payment)) AS total' ])
            ->leftJoin('product p', 'contract_detail.product_id = p.product_id')
            ->leftJoin('funding_plan fp', 'contract_detail.funding_plan_id = fp.funding_plan_id')
            ->leftJoin(['ppm' => $leftProductPrice], 'ppm.product_id = p.product_id')
            ->leftJoin('product_price pp', 'ppm.product_price_id = pp.product_price_id')
            ->andFilterWhere(['contract_detail.contract_id'=> $params['contract_id']]);

        $logs = ContractDetail::find()
            ->select(['contract_detail.contract_detail_id', 'cdl.contract_detail_log_id', 'p.type','p.name AS product_name',
                'cdl.date', 'cdl.from_date', 'cdl.to_date', 'cdl.status',
                'fp.qty_payments', 'fp.amount_payment',
                'IF(fp.funding_plan_id IS NULL, truncate(net_price + taxes, 2), (fp.qty_payments * fp.amount_payment)) AS total' ])
            ->leftJoin('contract_detail_log cdl', 'contract_detail.contract_detail_id = cdl.contract_detail_id')
            ->leftJoin('product p', 'cdl.product_id = p.product_id')
            ->leftJoin('funding_plan fp', 'cdl.funding_plan_id = fp.funding_plan_id')
            ->leftJoin(['ppm' => $leftProductPrice], 'ppm.product_id = p.product_id')
            ->leftJoin('product_price pp', 'ppm.product_price_id = pp.product_price_id')
            ->andFilterWhere(['contract_detail.contract_id'=> $params['contract_id']]);

        $details->union($logs, true);

        $query = new Query();
        $query->select(['*'])
            ->from(['b'=>$details])
            ->where('contract_detail_log_id is not null')
            ->orderBy(['contract_detail_id'=>SORT_ASC, 'contract_detail_log_id'=>SORT_ASC ]);

        return $query;
    }
}
