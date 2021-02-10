<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\AccessPoint;

/**
 * AccessPointSearch represents the model behind the search form of `app\modules\westnet\models\AccessPoint`.
 */
class AccessPointSearch extends AccessPoint
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_point_id', 'node_id'], 'integer'],
            [['name', 'status', 'strategy_class'], 'safe'],
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
        $query = AccessPoint::find();

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
            'access_point_id' => $this->access_point_id,
            'node_id' => $this->node_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'strategy_class', $this->strategy_class]);

        return $dataProvider;
    }
}
