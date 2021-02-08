<?php

namespace app\modules\firstdata\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\firstdata\models\FirstdataCompanyConfig;

/**
 * FirstdataCompanyConfigSearch represents the model behind the search form of `app\modules\firstdata\models\FirstdataCompanyConfig`.
 */
class FirstdataCompanyConfigSearch extends FirstdataCompanyConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_company_config_id', 'commerce_number', 'company_id'], 'integer'],
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
        $query = FirstdataCompanyConfig::find();

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
            'firstdata_company_config_id' => $this->firstdata_company_config_id,
            'commerce_number' => $this->commerce_number,
            'company_id' => $this->company_id,
        ]);

        return $dataProvider;
    }
}
