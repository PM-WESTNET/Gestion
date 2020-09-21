<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\NodeChangeProcess;

/**
 * NodeChangeProcessSearch represents the model behind the search form of `app\modules\westnet\models\NodeChangeProcess`.
 */
class NodeChangeProcessSearch extends NodeChangeProcess
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['node_change_process_id', 'node_id', 'creator_user_id'], 'integer'],
            [['created_at', 'ended_at', 'status'], 'safe'],
        ];
    }

    /**
     * Sobreescribo valores de inicializacion del padre
     */
    public function init()
    {
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
        $query = NodeChangeProcess::find();

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
            'node_change_process_id' => $this->node_change_process_id,
            'created_at' => $this->created_at,
            'ended_at' => $this->ended_at,
            'node_id' => $this->node_id,
            'creator_user_id' => $this->creator_user_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
