<?php

namespace app\modules\sale\modules\contract\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\modules\contract\models\ProgrammaticChangePlan;

/**
 * ProgrammaticChangePlanSearch represents the model behind the search form of `app\modules\sale\modules\contract\models\ProgrammaticChangePlan`.
 */
class ProgrammaticChangePlanSearch extends ProgrammaticChangePlan
{
    public $customer_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programmatic_change_plan_id', 'date', 'applied', 'created_at', 'updated_at', 'contract_id', 'product_id', 'user_id'], 'integer'],
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
        $query = ProgrammaticChangePlan::find();

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
            'programmatic_change_plan_id' => $this->programmatic_change_plan_id,
            'date' => $this->date,
            'applied' => $this->applied,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contract_id' => $this->contract_id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}
