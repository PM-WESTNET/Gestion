<?php

namespace app\modules\westnet\models\search;

use app\modules\sale\models\Customer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\Node;
use yii\db\Expression;
use yii\db\Query;

/**
 * NodeSearch represents the model behind the search form about `app\modules\westnet\models\Node`.
 */
class NodeSearch extends Node {

    public $parent_node_id;
    public $server_id;
    public $status;
    public $company_id;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['node_id', 'zone_id', 'code', 'company_id', 'subnet'], 'integer'],
            [['name', 'status', 'parent_node_id', 'server_id', 'status', 'company_id'], 'safe'],
        ];
    }
    
    

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Node::find();
        $query->innerJoin('server', 'node.server_id = server.server_id');
        $query->innerJoin('zone', 'node.zone_id = zone.zone_id');
        $query->leftJoin('node n2', 'node.parent_node_id = n2.node_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => new \yii\data\Sort([
                'attributes'=>[
                    'node.name',
                    'server.name',
                    'n2.name',
                    'node.status',
                    'zone.name',
                    'node.subnet'
                ]
            ]),
        ]);

        $this->load($params);

        /*         * if (!$this->validate()) {
          // uncomment the following line if you do not want to any records when validation fails
          // $query->where('0=1');
          return $dataProvider;
          }* */

        $query->andFilterWhere([
            'node.node_id' => $this->node_id,
            'node.zone_id' => $this->zone_id,
            'node.server_id' => $this->server_id,
            'node.parent_node_id' => $this->parent_node_id,
            'node.company_id' => $this->company_id,
            'node.subnet' => $this->subnet,
        ]);

        $query->andFilterWhere(['like', 'node.name', $this->name]);

        if (is_array($this->status)) {
            foreach ($this->status as $status) {
                $query->orFilterWhere(['=', 'node.status', $status]);
            }
        }

        return $dataProvider;
    }

    public function searchPossibleParentNodes($server_id, $node_id) {
        $query = Node::find()
                ->select(['node_id', 'name'])
                ->where(['=', 'server_id', $server_id]);
        if (isset($node_id)) {
            $query->andWhere(['not', ['node_id' => $node_id]]);
        }
        return $query->all();
    }

}
