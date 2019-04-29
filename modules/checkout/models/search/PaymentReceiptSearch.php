<?php

namespace app\modules\checkout\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\checkout\models\PaymentReceipt;

/**
 * PaymentReceiptSearch represents the model behind the search form about `app\modules\checkout\models\PaymentReceipt`.
 */
class PaymentReceiptSearch extends PaymentReceipt
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_receipt_id'], 'integer'],
            [['amount', 'balance'], 'number'],
            [['date', 'time', 'concept'], 'safe'],
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
        $query = PaymentReceipt::find();

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
            'payment_receipt_id' => $this->payment_receipt_id,
            'amount' => $this->amount,
            'date' => $this->date,
            'balance' => $this->balance,
            'customer_id' => $this->customer_id,
            'payment_method_id' => $this->payment_method_id
        ]);

        $query->andFilterWhere(['like', 'concept', $this->concept]);

        return $dataProvider;
    }
}
