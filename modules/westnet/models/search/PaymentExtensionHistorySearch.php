<?php

namespace app\modules\westnet\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\PaymentExtensionHistory;
use Yii;

/**
 * PaymentExtensionHistorySearch represents the model behind the search form of `app\modules\westnet\models\PaymentExtensionHistory`.
 */
class PaymentExtensionHistorySearch extends PaymentExtensionHistory
{
    public $date_from;
    public $date_to;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_extension_history_id', 'customer_id', 'created_at'], 'integer'],
            [['from', 'date', 'date_from', 'date_to'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date_from' => Yii::t('app', 'Date from'),
            'date_to' => Yii::t('app', 'Date to')
        ]);
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
        $query = PaymentExtensionHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'payment_extension_history_id' => $this->payment_extension_history_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'from', $this->from])
            ->andFilterWhere(['like', 'date', $this->date]);

        if($this->date_from) {
            $query->andFilterWhere(['>=', 'date', $this->date_from]);
        }

        if($this->date_to) {
            $query->andFilterWhere(['<=', 'date', $this->date_to]);
        }

        return $dataProvider;
    }
}
