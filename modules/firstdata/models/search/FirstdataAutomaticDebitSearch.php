<?php

namespace app\modules\firstdata\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\firstdata\models\FirstdataAutomaticDebit;

/**
 * FirstdataAutomaticDebitSearch represents the model behind the search form of `app\modules\firstdata\models\FirstdataAutomaticDebit`.
 */
class FirstdataAutomaticDebitSearch extends FirstdataAutomaticDebit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_automatic_debit_id', 'customer_id', 'company_config_id'], 'integer'],
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
        $query = FirstdataAutomaticDebit::find();

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
            'firstdata_automatic_debit_id' => $this->firstdata_automatic_debit_id,
            'customer_id' => $this->customer_id,
            'company_config_id' => $this->company_config_id,
        ]);

        return $dataProvider;
    }
}
