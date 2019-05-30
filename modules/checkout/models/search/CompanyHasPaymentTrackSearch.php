<?php

namespace app\modules\checkout\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\checkout\models\CustomerHasPaymentTrack;

/**
 * CompanyHasPaymentTrackSearch represents the model behind the search form of `app\modules\sale\models\CustomerHasPaymentTrack`.
 */
class CompanyHasPaymentTrackSearch extends CustomerHasPaymentTrack
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_has_payment_track', 'customer_id', 'payment_method_id', 'track_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = CustomerHasPaymentTrack::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer_has_payment_track' => $this->customer_has_payment_track,
            'customer_id' => $this->customer_id,
            'payment_method_id' => $this->payment_method_id,
            'track_id' => $this->track_id,
        ]);

        return $dataProvider;
    }
}
