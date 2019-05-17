<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\AdsPercentagePerCompany;

/**
 * AdsPercentagePerCompanySearch represents the model behind the search form of `app\modules\westnet\models\AdsPercentagePerCompany`.
 */
class AdsPercentagePerCompanySearch extends AdsPercentagePerCompany
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['percentage_per_company_id', 'parent_company_id', 'company_id'], 'integer'],
            [['percentage'], 'number'],
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
        $query = AdsPercentagePerCompany::find();

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
            'percentage_per_company_id' => $this->percentage_per_company_id,
            'parent_company_id' => $this->parent_company_id,
            'company_id' => $this->company_id,
            'percentage' => $this->percentage,
        ]);

        return $dataProvider;
    }
}
