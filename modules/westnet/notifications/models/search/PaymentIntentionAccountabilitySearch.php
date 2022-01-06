<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;

/**
 * DocumentTypeSearch represents the model behind the search form about `app\modules\sale\models\DocumentType`.
 */
class PaymentIntentionAccountabilitySearch extends PaymentIntentionAccountability
{   

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
        $query = PaymentIntentionAccountability::find()
            ->alias('pia')
            ->leftJoin('customer cus', 'cus.customer_id = pia.customer_id')
            ->leftJoin('company com', 'com.company_id = cus.company_id')
            ->orderBy(['payment_intention_accountability_id' => SORT_DESC]); // to show recent payments first

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'concat(concat(cus.lastname," ",cus.name), " ", cus.code)', $this->customer_name]);
        $query->andFilterWhere(['like', 'pia.siro_payment_intention_id', $this->siro_payment_intention_id.'%',false]);
        if(!empty($this->payment_id)) $query->andFilterWhere(['like', 'pia.payment_id', $this->payment_id.'%',false]); // this line has the empty() foo because the wildcard of the Like operator does not give back Null values (no payment ID)
        $query->andFilterWhere(['like', 'pia.customer_id', $this->customer_id.'%',false]);
        $query->andFilterWhere(['like', 'pia.payment_intention_accountability_id', $this->payment_intention_accountability_id.'%',false]);
        
        $query->andFilterWhere(['=', 'com.name', $this->company_name]); //this is an index, not a string name
        $query->andFilterWhere(['like', 'pia.total_amount', $this->total_amount.'%',false]); //monto
        $query->andFilterWhere(['=', 'pia.payment_method', $this->payment_method]); //metodo pago
        $query->andFilterWhere(['=', 'pia.status', $this->status]); //Estado *importante
        $query->andFilterWhere(['=', 'pia.collection_channel_description', $this->collection_channel_description]); //canal de collection_channel_description
        
        //$query->andFilterWhere(['=', 'pia.payment_method', $this->payment_method]); //fec. pago
        //$query->andFilterWhere(['=', 'pia.payment_method', $this->payment_method]); //fec. acreditacion

        return $dataProvider;
    }
}
