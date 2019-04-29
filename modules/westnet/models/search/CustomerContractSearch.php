<?php

namespace app\modules\westnet\models\search;

use app\modules\sale\models\Customer;
use app\modules\westnet\models\Connection;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;

/**
 * NodeSearch represents the model behind the search form about `app\modules\westnet\models\Node`.
 */
class CustomerContractSearch extends Customer
{

    /** @var  $company_id  */
    public $company_id;

    /** @var  $new_company_id  */
    public $new_company_id;

    /** @var  $new_product_id  */
    public $new_product_id;

    /** @var  $product_id  */
    public $product_id;

    /** @var  $customer_category_id */
    public $customer_category_id;

    /** @var  $node_id */
    public $node_id;

    /** @var  $server_id */
    public $server_id;

    /** @var  $discount_id */
    public $discount_id;

    /** @var  $from_date */
    public $from_date;

    /** @var  $to_date */
    public $to_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['server_id', 'node_id', 'product_id', 'customer_category_id', 'company_id', 'discount_id'], 'integer'],
            [['server_id', 'node_id', 'product_id', 'customer_category_id', 'company_id', 'discount_id', 'from_date', 'to_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'company_id' => Yii::t('app', 'Company'),
            'server_id' => Yii::t('westnet', 'Server'),
            'node_id' => Yii::t('westnet', 'Node'),
            'product_id' => Yii::t('westnet', 'Plan'),
            'customer_category_id' => Yii::t('app', 'Customer Category'),
        ]);
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

        /** @var Query $query */
        $query = new Query();
        $today = (new \DateTime('now'))->format('Y-m-d');
        // Armo la consulta general, con los filtros por defecto, en caso de no tener parametros deberia de traer algo.
        $query
            ->select([new Expression('distinct c.customer_id'), new Expression('concat(c.lastname, \' \',  c.name) as customer'), 'p.name as plan', 'co.contract_id', 'cd.contract_detail_id', 'con.connection_id'])
            ->from('customer as c')
            ->leftJoin('customer_category_has_customer ccc', 'c.customer_id = ccc.customer_id')
            ->leftJoin('contract co', 'c.customer_id = co.customer_id')
            ->leftJoin('contract_detail cd', 'co.contract_id = cd.contract_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('connection con', 'co.contract_id = con.contract_id')
            ->where("co.contract_id is not null and co.status = 'active' and co.to_date is null and p.type = 'plan' and cd.from_date <= '$today' and cd.to_date is null");


        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=2');
            return $query;
        }

        // Filtro el producto
        if($this->product_id) {
            $query->andFilterWhere(['p.product_id'=>$this->product_id]);
        }

        // Filtro el Customer class
        if($this->customer_category_id) {
            $query->andFilterWhere(['ccc.customer_category_id'=>$this->customer_category_id]);
        }

        // Filtro el Nodo
        if($this->node_id) {
            $query->andFilterWhere(['con.node_id'=>$this->node_id]);
        }

        // Filtro el Servidor
        if($this->server_id) {
            $query->andFilterWhere(['con.server_id'=>$this->server_id]);
        }

        // Filtro el Servidor
        if($this->company_id) {
            $query->andFilterWhere(['c.company_id'=>$this->company_id]);
        }

        $query->orderBy(['c.name'=>SORT_ASC]);

        return $query;
    }

}