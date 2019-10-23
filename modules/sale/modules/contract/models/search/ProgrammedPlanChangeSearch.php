<?php

namespace app\modules\sale\modules\contract\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;

/**
 * ProgrammedPlanChangeSearch represents the model behind the search form of `app\modules\sale\modules\contract\models\ProgrammedPlanChange`.
 */
class ProgrammedPlanChangeSearch extends ProgrammedPlanChange
{
    public $customer_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programmed_plan_change_id', 'applied', 'created_at', 'updated_at', 'contract_id', 'product_id', 'user_id', 'customer_id'], 'integer'],
            [['date'], 'safe']
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
        $query = ProgrammedPlanChange::find();

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
            'programmed_plan_change_id' => $this->programmed_plan_change_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
        ]);

        if($this->date) {
            $date = (new \DateTime($this->date))->getTimestamp();
            $query->andFilterWhere(['date' => $date]);
        }

        if($this->customer_id) {
            $query->leftJoin('contract', 'contract.contract_id = programmed_plan_change.contract_id')
                ->andFilterWhere(['contract.customer_id' => $this->customer_id]);
        }

        if($this->applied) {
            $query->andFilterWhere(['applied' => 1]);
        } else {
            $query->andFilterWhere(['applied' => null]);
        }

        $query->orderBy(['date' => SORT_DESC]);

        return $dataProvider;
    }
}
