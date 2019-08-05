<?php

namespace app\modules\automaticdebit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\automaticdebit\models\BankCompanyConfig;

/**
 * BankCompanyConfigSearch represents the model behind the search form of `app\modules\automaticdebit\models\BankCompanyConfig`.
 */
class BankCompanyConfigSearch extends BankCompanyConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_company_config_id', 'company_id', 'bank_id', 'created_at', 'updated_at'], 'integer'],
            [['company_identification', 'branch', 'control_digit', 'account_number'], 'safe'],
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
        $query = BankCompanyConfig::find();

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
            'bank_company_config_id' => $this->bank_company_config_id,
            'company_id' => $this->company_id,
            'bank_id' => $this->bank_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'company_identification', $this->company_identification])
            ->andFilterWhere(['like', 'branch', $this->branch])
            ->andFilterWhere(['like', 'control_digit', $this->control_digit])
            ->andFilterWhere(['like', 'account_number', $this->account_number]);

        return $dataProvider;
    }
}
