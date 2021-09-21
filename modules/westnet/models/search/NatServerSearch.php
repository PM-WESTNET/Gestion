<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\NatServer;
use app\modules\westnet\models\Node;

/**
 * EmptyAdsSearch represents the model behind the search form about `app\modules\westnet\models\EmptyAds`.
 */
class NatServerSearch extends NatServer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nat_server_id'], 'integer'],
            [['description','created_at','updated_at'], 'safe'],
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
        $query = NatServer::find();

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
            'nat_server_id' => $this->nat_server_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance
     *
     *
     * @return ActiveDataProvider
     */
    public function searchNodes($id)
    {
        $query = Node::find()->where(['nat_server_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
