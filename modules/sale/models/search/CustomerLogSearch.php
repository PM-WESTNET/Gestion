<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\CustomerLog;

/**
 * CustomerLog represents the model behind the search form about `app\modules\sale\models\CustomerLog`.
 */
class CustomerLogSearch extends CustomerLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_log_id', 'customer_id', 'user_id'], 'integer'],
            [['action', 'before_value', 'new_value', 'date', 'observations'], 'safe'],
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
        $query = CustomerLog::find();

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
            'customer_log_id' => $this->customer_log_id,
            'date' => $this->date,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'before_value', $this->before_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value])
            ->andFilterWhere(['like', 'observations', $this->observations]);
        
        $query->orderBy(['date' => SORT_DESC]);
        return $dataProvider;
    }
}
