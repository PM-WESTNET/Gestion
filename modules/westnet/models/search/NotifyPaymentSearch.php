<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\NotifyPayment;

/**
 * NotifyPaymentSearch represents the model behind the search form of `app\modules\westnet\models\NotifyPayment`.
 */
class NotifyPaymentSearch extends NotifyPayment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notify_payment_id', 'payment_method_id', 'created_at'], 'integer'],
            [['date', 'image_receipt'], 'safe'],
            [['amount'], 'number'],
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
        $query = NotifyPayment::find();

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
            'notify_payment_id' => $this->notify_payment_id,
            'date' => $this->date,
            'amount' => $this->amount,
            'payment_method_id' => $this->payment_method_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'image_receipt', $this->image_receipt]);

        return $dataProvider;
    }
}
