<?php

namespace app\modules\westnet\models\search;

use app\modules\westnet\models\Connection;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * NodeSearch represents the model behind the search form about `app\modules\westnet\models\Node`.
 */
class ConnectionSearch extends Connection
{

    public $customer_id;
    public $ip;
    public $customer_class_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'string'],
            [['server_id', 'node_id', 'customer_id', 'customer_class_id'], 'integer'],
            [['customer_id', 'ip'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'server_id' => Yii::t('westnet', 'Server'),
            'customer_id' => Yii::t('app', 'Customer'),
        ]);
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
     * Return all the IPs assigned
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        $subQueryCategory = (new Query())
            ->select(['customer_id', 'customer_category_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_category_has_customer')
            ->groupBy(['customer_id']);
        
        $query = Connection::find();
        $query->leftJoin('contract con', 'connection.contract_id = con.contract_id')
            ->leftJoin('customer c', 'con.customer_id = c.customer_id')
            ->leftJoin(['cat' => $subQueryCategory], 'cat.customer_id = con.customer_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('1=2');
            return $dataProvider;
        }

        if($this->node_id) {
            $query->andFilterWhere(['node_id'=>$this->node_id]);
        }
        
        if($this->server_id) {
            $query->andFilterWhere(['server_id'=>$this->server_id]);
        }

        if($this->ip) {
            $ip_arr = explode('.', str_replace('_','', $this->ip));

            $ip = ($ip_arr[0]?$ip_arr[0]:'0').".".($ip_arr[1]?$ip_arr[1]:'0').".".
            ($ip_arr[2]?$ip_arr[2]:'0').".".($ip_arr[3]?$ip_arr[3]:'0');
            $ip = ip2long($ip);

            $isEqual = $ip_arr[2] || $ip_arr[3];

            $query->orFilterWhere([
                'or',[($isEqual ? '=' :  '>='), 'ip4_1', $ip],
            ]);
            $query->orFilterWhere([
                'or',[($isEqual ? '=' :  '>='), 'ip4_2', $ip],
            ]);
            $query->orFilterWhere([
                'or',[($isEqual ? '=' :  '>='), 'ip4_public', $ip],
            ]);
        }

        if($this->customer_id) {
            $query->andFilterWhere(['c.customer_id'=> $this->customer_id]);
        }
        
        if ($this->customer_class_id) {
            $query->andFilterWhere(['cat.customer_category_id' => $this->customer_class_id]);
        }

        $query->orderBy(['server_id'=>SORT_ASC, 'node_id'=>SORT_ASC, 'ip4_1'=>SORT_ASC]);

        return $dataProvider;
    }

    public function findByServer($server_id)
    {
        return Connection::find()
                ->andWhere(['server_id'=>$server_id])
                ->andFilterWhere(['<>', 'status', Connection::STATUS_DISABLED]);
    }

    public function findByServerToRestore($server_id)
    {
        return Connection::find()
            ->leftJoin('node', 'connection.node_id = node.node_id')
            ->where(['connection.server_id'=>$server_id, 'connection.status'=>[Connection::STATUS_ENABLED]])
            ->andFilterWhere( ['<>', 'connection.server_id', 'node.server_id']);
    }
}