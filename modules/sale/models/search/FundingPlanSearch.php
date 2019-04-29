<?php

namespace app\modules\sale\models\search;

use app\modules\sale\models\Product;
use app\modules\sale\models\ProductPrice;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\FundingPlan;
use yii\db\Query;

/**
 * FundingPlanSearch represents the model behind the search form about `app\modules\sale\models\FundingPlan`.
 */
class FundingPlanSearch extends FundingPlan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['funding_plan_id', 'qty_payments'], 'integer'],
            [['amount_payment'], 'number'],
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
        $query = FundingPlan::find();

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
            'funding_plan_id' => $this->funding_plan_id,
            'qty_payments' => $this->qty_payments,
            'amount_payment' => $this->amount_payment,
        ]);

        return $dataProvider;
    }

    /**
     * Retorna los planes de pago si existen.
     *
     * @param $product_id
     * @param bool|true $include_product_price
     * @return \yii\db\ActiveQuery
     */
    public function searchByProduct($product_id, $count, $include_product_price = true)
    {
        $result = [];

        $product = Product::findOne(['product_id'=>$product_id]);
        
        $result[] = [
            'id' => "0",
            'name' => '1 ' . Yii::t('app', 'payment of') . ' '. Yii::$app->formatter->asCurrency($product->getFinalPrice()* $count)
        ];


        foreach( $product->getFundingPlan()->where(['status'=>'enabled'])->all() as $key=>$fundingPlan) {
            $result[$fundingPlan->funding_plan_id] = [
                'id' => $fundingPlan->funding_plan_id,
                'name' => $fundingPlan->qty_payments . ' ' . Yii::t('app', 'payments of') . ' ' . Yii::$app->formatter->asCurrency($fundingPlan->getFinalAmount())
            ];
        }

        return $result;
    }
}