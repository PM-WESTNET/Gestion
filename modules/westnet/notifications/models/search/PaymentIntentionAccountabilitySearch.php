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
    public $collection_channel_description;
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

        $query = PaymentIntentionAccountability::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'payment_intention_accountability_id' => $this->payment_intention_accountability_id,
            'customer_id' => $this->customer_id,
            'siro_payment_intention_id' => $this->siro_payment_intention_id,
            'payment_id' => $this->payment_id,
        ]);

        //$query->andFilterWhere(['like', 'concat(concat(c.lastname," ",c.name), " ", c.code)', $this->collection_channel_description]);

        //$query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
