<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 15/08/17
 * Time: 12:02
 */

namespace app\modules\westnet\reports\search;


use app\components\helpers\DbHelper;
use app\modules\westnet\models\Node;
use app\modules\sale\models\Customer;
use app\modules\westnet\reports\ReportsModule;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;

class CustomerSearch extends Model
{

    const LAST_WEEK_RANGE= 'last_week';
    const LAST_MONTH_RANGE = 'last_month';
    const LAST_YEAR_RANGE = 'last_year';

    public $date_from;
    public $date_to;

    // Rango de tiempo para el reporte de clientes actualizados
    public $range;

    public function init()
    {
        parent::init();
        $this->date_from = (new \DateTime('first day of this month'))->format('01-01-Y');
        $this->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['date_from', 'date_to'], 'string'],
            [['date_from', 'date_to', 'range'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => ReportsModule::t('app', 'Date From'),
            'date_to' => ReportsModule::t('app', 'Date To'),
            'range' => Yii::t('app', 'Time Range')
        ];
    }


    public function findPerMonth($params)
    {
        $this->load($params);

        $query = new Query();
        $query
            ->select([new Expression('count(DISTINCT cus.customer_id) cant'), new Expression('date_format(con.date, \'%m/%Y\') as periodo')])
            ->from('customer cus')
            ->leftJoin('contract con', 'cus.customer_id = con.customer_id')
            ->andWhere(new Expression("con.status <> 'draft'"))
            ->andWhere(new Expression('(con.to_date is null or ( date_format(con.to_date, \'%d\') > 28 and date_format(con.to_date, \'%d\') <= 31 ) )'))
            ->groupBy([new Expression('date_format(con.date, \'%m/%Y\')')])
            ->orderBy(['date_format(con.date, \'%Y%m\')' =>SORT_ASC])
        ;

        $mainQuery = new Query();
        $mainQuery->select(new Expression('cant, coalesce(periodo, \'06/2002\') as periodo, @cant:=@cant + cant as total'))
                  ->from(['c'=>$query, '(SELECT @cant :=0) as tc'])
        ;

        return $mainQuery->all();
    }

    public function findDifferenceByMonth()
    {
/*
select periodo, sum(alta) as alta, sum(baja) as baja, sum(alta) - sum(baja) as diferencia from (
  SELECT
    date_format(c.from_date, '%Y-%m') AS periodo,
    count(*)                          AS alta,
    0                                 AS baja
  FROM contract c
  WHERE c.status = 'active'
  GROUP BY date_format(c.from_date, '%Y-%m')
  UNION ALL
  SELECT
    date_format(to_date, '%Y-%m') AS periodo,
    0                                baja,
    count(*)                      AS baja
  FROM contract
  WHERE status = 'low'
  GROUP BY date_format(to_date, '%Y-%m')
) t
GROUP BY periodo
;
 * */

    }

    /**
     * Retorno la cantidad de contratos dados de baja por motivo y fecha
     *
     * @param $param
     */
    public function findByLowReason($param)
    {
        $this->load($param);

        $query = new Query();
        $query
            ->select(['cat.name', new Expression('date_format(con.date, \'%m/%Y\') as period'), 'count(con.contract_id) as cant'])
            ->from('contract con')
            ->innerJoin( DbHelper::getDbName(Yii::$app->dbticket) .'.category as cat', 'con.category_low_id = cat.category_id')
            ->groupBy(['cat.name', new Expression('date_format(con.date, \'%m/%Y\')')])
            ->orderBy(['date_format(con.date, \'%Y%m\')' =>SORT_ASC])
        ;

        if($this->date_from) {
            $query->andWhere(['>=', 'con.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if($this->date_to) {
            $query->andWhere(['<=', 'con.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        return $query->all();
    }

    public function findByNode($params) {
        $cS= new \app\modules\sale\models\search\CustomerSearch();

        $subquery = $cS->buildSearchQuery($params);

        $subquery->select(['customer.*','n.name as node', 'n.node_id as node_id']);
        $subquery->innerJoin('node n', 'n.node_id=connection.node_id');

        Yii::info($subquery->createCommand()->getRawSql());

        $query= (new Query())
            ->select(['c100.node_id', 'c100.node', 'COUNT(c100.customer_id) as total'])
            ->from(['c100' => $subquery])
            ->groupBy(['c100.node_id']);

        $data = $query->all();

        $result= [];

        $nodesQuery = Node::find();

        if (isset($params['CustomerSearch']['node_id']) && !empty($params['CustomerSearch']['node_id'])){
            $nodesQuery->andFilterWhere(['node_id' => $params['CustomerSearch']['node_id']]);
        }

        $nodes = $nodesQuery->all();

        Yii::info($data);
        Yii::info($nodes);

        foreach ($nodes as $node) {
            $result[$node->node_id]= [
                'node' => $node->name,
                'total' => 0
            ] ;
        }

        foreach ($data as $node) {

            $result[$node['node_id']] = [
                'node' => $node['node'],
                'total' => $node['total']
            ];
        }

        Yii::info($result);

        $dataProvider = new ArrayDataProvider(['allModels' => $result]);

        return $dataProvider;

    }

    private function filterByNode($query){
        if (!empty($this->node_id)) {
            $query->andWhere(['connection.node_id' => $this->node_id]);
        }
    }

    private function filterByNodes($query){
        if (!empty($this->nodes)) {
            $query->andWhere(['connection.node_id' => $this->nodes]);
        }
    }

    private function filterByPlan($query){
        if (!empty($this->plan_id)) {
            $query->andWhere(['contract_detail.product_id' => $this->plan_id]);
        }
    }

    private function filterByStatusAccount($query){
        if (!empty($this->connection_status)) {
            $query->andWhere(['connection.status_account' => $this->connection_status]);
        }
    }

    private function filterByContractStatus($query){

        if (!empty($this->contract_status)) {
            $query->andWhere(['contract.status' => $this->contract_status]);
        }

        if (!empty($this->not_contract_status)) {
            $query->andWhere(['not',['contract.status' => $this->not_contract_status]]);
        }
    }

    private function filterByZone($query){
        if (!empty($this->zone_id)) {
            $query->andWhere(['add.zone_id' => $this->zone_id]);
        }
    }

    private function filterByCompany($query, $parent = false){
        if (!empty($this->company_id)) {
            if($parent) {
                $query->andWhere(['customer.parent_company_id' => $this->company_id]);
            } else {
                $query->andWhere(['customer.company_id' => $this->company_id]);
            }
        }
    }

    private function filterByClass($query){
        if (!empty($this->customer_class_id)) {
            $query->andWhere(['cchc.customer_class_id' => $this->customer_class_id]);
        }
    }

    private function filterByCategory($query){
        if (!empty($this->customer_category_id)) {
            $query->andWhere(['ccathc.customer_category_id' => $this->customer_category_id]);
        }
    }


    /**
     * Datos para reporte de Clientes Actualizdos
     */
    public function findByCustomersUpdated($params)
    {
        $this->load($params);

        $labels = [];
        $points = [];


        if (!empty($this->date_from)) {
            $from_date = strtotime(Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd'));
        }else {
            $from_date = (new \DateTime())->modify('first day of month')->getTimestamp();
        }

        if (!empty($this->date_from)) {
            $to_date = strtotime(Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd'));
        }else {
            $to_date = (new \DateTime())->getTimestamp();
        }

        $label_format = 'dd/MM/yyyy';
        $step = 86400;

        if (($to_date - $from_date) < (86400 * 30)){
            $step = 86400;
            $label_format = 'dd/MM/yyyy';
        }

        if (($to_date - $from_date) < (86400 * 180) && ($to_date - $from_date) > (86400 * 30)){
            $step = 86400 * 15;
            $label_format = 'dd/MM/yyyy';
        }

        if (($to_date - $from_date) > (86400 * 180)) {
            $step = 86400 * 31;
            $label_format = 'MM/yyyy';
        }

        $before = 0;
        $counter = 0;
        for ($year= $from_date; $year <= $to_date; $year=$year + $step) {
            $labels[] = Yii::$app->formatter->asDate($year, $label_format);

            if($before === 0) {
                $qty = Customer::find()
                    ->andWhere(['=', 'last_update', Yii::$app->formatter->asDate($year, 'yyyy-MM-dd')])
                    ->count();
                $counter += $qty;
            }else {
                $qty = Customer::find()
                    ->andWhere(['<=', 'last_update', Yii::$app->formatter->asDate($year, 'yyyy-MM-dd')])
                    ->andFilterWhere(['>=', 'last_update', $before])
                    ->count();
            }
            $counter  += $qty;
            $points[] = [
                'x' => Yii::$app->formatter->asDate($year, 'dd/MM'),
                'y' => $counter
            ];

            $before = Yii::$app->formatter->asDate($year, 'yyyy-MM-dd');
        }

        return [
            'labels' => $labels,
            'points' => $points,
        ];


    }

    public function findByCustomersUpdatedByUser($params)
    {
        $query = (new Query);

        $query->select(['COUNT(*) as count', 'user_id', 'date']);
        $query->from('customer_update_register');

        $this->load($params);

        if (!empty($this->date_from)) {
            $from_date = strtotime(Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd'));
        }else {
            $from_date = (new \DateTime())->modify('first day of month')->getTimestamp();
        }

        if (!empty($this->date_from)) {
            $to_date = strtotime(Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd'));
        }else {
            $to_date = (new \DateTime())->getTimestamp();
        }

        $query->andWhere(['>=', 'date', $from_date]);
        $query->andWhere(['<=', 'date', $to_date]);

        $query->groupBy(['user_id']);
        
        return $query;
    } 
}