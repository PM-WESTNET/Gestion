<?php

namespace app\modules\cobrodigital\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cobrodigital\models\PaymentCard;

/**
 * PaymentCardSearch represents the model behind the search form of `app\modules\cobrodigital\models\PaymentCard`.
 */
class PaymentCardSearch extends PaymentCard
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_card_id', 'payment_card_file_id', 'used'], 'integer'],
            [['code_19_digits', 'code_29_digits', 'url'], 'safe'],
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
        $query = PaymentCard::find();

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
            'payment_card_id' => $this->payment_card_id,
            'payment_card_file_id' => $this->payment_card_file_id,
            'used' => $this->used,
        ]);

        $query->andFilterWhere(['like', 'code_19_digits', $this->code_19_digits])
            ->andFilterWhere(['like', 'code_29_digits', $this->code_29_digits])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
