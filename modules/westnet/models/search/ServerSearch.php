<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\Node;

/**
 * NodeSearch represents the model behind the search form about `app\modules\westnet\models\Node`.
 */
class ServerSearch extends Server
{
    public $origin_server_id;
    public $destiny_server_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_server_id', 'desntiy_server_id'], 'integer'],
            [['origin_server_id', 'desntiy_server_id'], 'required'],
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
    public function searchCustomers($params)
    {
        $query = Server::find();

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return null;
        }

        $query->andFilterWhere([
            'node_id' => $this->node_id,
            'zone_id' => $this->zone_id,
            'code' => $this->code,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $query;
    }
}
