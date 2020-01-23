<?php

namespace app\modules\accounting\models\search;

use app\modules\accounting\models\Account;
use app\modules\accounting\models\AccountMovement;
use Codeception\Util\Debug;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 4/08/15
 * Time: 16:10
 */
class AccountMovementSearch extends AccountMovement {

    public $balance;
    public $debit;
    public $credit;
    // Fechas
    public $toDate;
    public $fromDate;
    public $date;
    public $initStatusDate;
    // Cuentas
    public $account_id_from;
    public $account_id_to;
    // Estados de movimiento
    public $statuses;
    public $status;
    public $totalDebit;
    public $totalCredit;
    public $account_movement_item_id;
    public $account;
    public $account_id;
    //Tiempo
    public $fromTime;
    public $toTime;
    public $fromDatetime;
    public $toDatetime;

    //Conciliaciones
    public $cuit;
    public $cuit2;

        
    public function rules() {
        $statuses = ['draft', 'closed', 'conciled', 'broken'];

        return [
            [['debit', 'credit'], 'number'],
            [['account_id_from', 'account_id_to', 'account_id'], 'integer'],
            [['toDate', 'fromDate', 'date', 'cuit', 'cuit2'], 'safe'],
            [['toDate', 'fromDate', 'date'], 'default', 'value' => null],
            [['status'], 'in', 'range' => $statuses],
            ['statuses', 'each', 'rule' => ['in', 'range' => $statuses]],
            ['company_id', 'integer'],
            [['account_movement_id', 'toTime', 'description', 'fromDate', 'fromDatetime', 'toDatetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'statuses' => Yii::t('app', 'Statuses'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'date' => Yii::t('app', 'Date'),
            'account_id_from' => Yii::t('accounting', 'Account Id From'),
            'account_id_to' => Yii::t('accounting', 'Account Id To'),
            'account_id' => Yii::t('accounting', 'Includes account'),
        ]);
    }

    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function init() {

        parent::init();
        $this->account_id_from = null;
        $this->account_id_to = null;
    }

    /**
     * Busqueda regular
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params, $mode = 0, $all = false) {

        $this->load($params);

        $subQuery = (new Query())
                ->select([
                    'am.check',
                    'am.account_movement_id',
                    'am.description',
                    'am.date',
                    'am.time',
                    'IF(isnull(ami.debit), 0, ami.debit) as debit',
                    'IF(isnull(ami.credit), 0, ami.credit) as credit',
                    'ami.account_movement_item_id',
                    'ami.status',
                    'mba.money_box_account_id',
                    'ac2.name AS from'])
                ->from('account_movement am')
                ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
                ->leftJoin('account ac', 'ami.account_id = ac.account_id')
                ->leftJoin('money_box_account mba', 'ac.account_id = mba.account_id')
                ->leftJoin('account_movement_item ami2', 'am.account_movement_id = ami2.account_movement_id AND ami2.account_movement_item_id <> ami.account_movement_item_id')
                ->leftJoin('account ac2', 'ami2.account_id = ac2.account_id')
                ->where('ac.lft between ' . $this->account_id_from . ' and ' . $this->account_id_to)
                ->groupBy(['am.date', 'ami.account_movement_item_id', 'ami.status'])
                ->orderBy('am.date, am.time');
        /**
        $queryTotals = new Query();
        $queryTotals->select(['sum(c.debit) as debit', 'sum(c.credit) as credit']);
        $queryTotals->from(['c' => $subQuery]);
         **/
        $this->filterDates($subQuery, 'am', $all);
        $this->filterTimes($subQuery, 'am');
        $this->filterDatetime($subQuery, 'am');
        $this->filterStatus($subQuery, 'am');
        $rsTotals = $this->statusAccount(true);

        $this->totalDebit = $rsTotals['debit'];
        $this->totalCredit = $rsTotals['credit'];

        if (isset($this->toDate)) {
            $this->date = $this->toDate;
        }

        $status_account = $this->statusAccount();
        Yii::$app->db->createCommand('set @partial_balance := ' . $status_account['balance'] . ';')->execute();

        $query = (new Query())
                ->select(['*,  @partial_balance:=@partial_balance + (c.debit - c.credit) AS partial_balance'])
                ->from(['c' => $subQuery]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'account_movement_item_id',
            'pagination' => false,
        ]);

        if ($mode == 0) {
            return $dataProvider;
        } else {
            Debug::debug($query->createCommand()->getRawSql());
            return $query->all();
        }
    }

    public function searchForDailyBox($params) {
        $this->load($params);

        $subQuery = (new Query())
                ->select([
                    'am.account_movement_id',
                    'am.description',
                    'am.date',
                    'IF(isnull(ami.debit), 0, ami.debit) as debit',
                    'IF(isnull(ami.credit), 0, ami.credit) as credit',
                    'ami.account_movement_item_id',
                    'ami.status',
                    'mba.money_box_account_id',
                    'ac2.name AS from'])
                ->from('account_movement am')
                ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
                ->leftJoin('account ac', 'ami.account_id = ac.account_id')
                ->leftJoin('money_box_account mba', 'ac.account_id = mba.account_id')
                ->leftJoin('account_movement_item ami2', 'am.account_movement_id = ami2.account_movement_id AND ami2.account_movement_item_id <> ami.account_movement_item_id')
                ->leftJoin('account ac2', 'ami2.account_id = ac2.account_id')
                ->where('ac.lft between ' . $this->account_id_from . ' and ' . $this->account_id_to)
                ->groupBy(['am.date', 'ami.account_movement_item_id', 'ami.status'])
                ->orderBy('am.date');
        
        if(count($params)> 0){
            $this->filterDates($subQuery, 'am');
        }
        $queryTotals = new Query();
        $queryTotals->select(['sum(coalesce(c.debit,0)) as debit', 'sum(coalesce(c.credit)) as credit']);
        $queryTotals->from(['c' => $subQuery]);
        $rsTotals = $queryTotals->one();
        
        //$rsTotals = $this->statusAccount(true);

        $this->totalDebit = ($rsTotals['debit'] == null ? 0 : $rsTotals['debit']);
        $this->totalCredit = ($rsTotals['credit'] == null ? 0 : $rsTotals['credit']);
        
        
        if(count($params)=== 0){
            $this->filterDates($subQuery, 'am');
        }
        
        $status_account= $this->statusAccount();

        Yii::$app->db->createCommand('set @partial_balance := ' . $status_account['balance'] . ';')->execute();



        $query = (new Query())
                ->select(['*,  @partial_balance:=@partial_balance + (c.debit - c.credit) AS partial_balance'])
                ->from(['c' => $subQuery]);

       



        /**$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'account_movement_item_id',
            'pagination' => false,
        ]);**/


        return $query->all();
    }

    public function searchForConciliation($params) {
        $query = (new Query())
                ->select(['ami.account_movement_item_id', 'account_movement.date', 'account_movement.account_movement_id', 'account_movement.description', 'account_movement.status', '(coalesce(ami.debit,0)) as debit', '(coalesce(ami.credit,0)) as credit'])
                ->from('account_movement')
                ->leftJoin('account_movement_item ami', 'account_movement.account_movement_id = ami.account_movement_id')
                ->leftJoin('conciliation_item_has_account_movement_item cami', 'ami.account_movement_item_id = cami.account_movement_item_id');
                //->leftJoin('conciliation_item ci', 'cami.conciliation_item_id = ci.conciliation_item_id');


        // join con cuentas
        $query->leftJoin('account', 'ami.account_id = account.account_id');
        // $query->groupBy(['account_movement.date', 'account_movement.description', 'account_movement.status']);

        $this->load($params);

//        if (!$this->validate()) {
//            return $dataProvider;
//        }
        $query->andFilterWhere(['between', 'account.lft', $this->account_id_from, $this->account_id_to])
                ->andFilterWhere(['company_id' => $this->company_id]);

        $query->andFilterWhere(['like', 'description', $this->description]);
        //Estado/s de factura
        $this->filterStatus($query);

        //Fechas
        $this->filterDates($query);

        $this->filterBalance($query);

        // Totales
        $queryTotals = clone $query;
        $queryTotals->select(['sum(coalesce(ami.debit,0)) as debit', 'sum(coalesce(ami.credit,0)) as credit']);
        $queryTotals->groupBy("");
        $rsTotals = $queryTotals->one();

        //Aplicamos este filtro despues de clonar para saber el saldo de la cuenta
        $query->andWhere(['IS', 'cami.conciliation_item_id', null]);

        $this->totalDebit = $rsTotals['debit'];
        $this->totalCredit = $rsTotals['credit'];

        $query->orderBy(['date' => SORT_DESC]);

        $models = $query->all();

        if ($this->cuit) {
            $models = $this->filterByCustomerDocumentNumber($this->cuit, $models);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
//            'sort' => [
//                'defaultOrder' => ['date' => SORT_ASC]
//            ],
            'key' => 'account_movement_item_id'
        ]);
        $dataProvider->setPagination(false);

        return $dataProvider;
    }
    
    public function searchForMovements($params, $mode= 0)
    {

        $this->load($params);

        $query = (new Query())
            ->select(new Expression('DISTINCT account_movement.account_movement_id'))
            ->from('account_movement')
            ->leftJoin('account_movement_item ami', 'account_movement.account_movement_id = ami.account_movement_id')
            ->leftJoin('account', 'ami.account_id = account.account_id')
        ;

        $mainQuery = (new Query())
            ->select(['ami.account_movement_item_id', 'account_movement.account_movement_id','account_movement.date', 'account_movement.description', 'account_movement.status', 'account.name as account', 'sum(coalesce(ami.debit,0)) as debit', 'sum(coalesce(ami.credit,0)) as credit'])
            ->from('account_movement')
            ->leftJoin('account_movement_item ami', 'account_movement.account_movement_id = ami.account_movement_id')
            ->leftJoin('account', 'ami.account_id = account.account_id')
            ->groupBy(['ami.account_movement_item_id', 'account_movement.account_movement_id', 'account.name'])
            ->orderBy(['account_movement.date'=>SORT_DESC, 'account_movement.account_movement_id'=>SORT_DESC, 'debit'=>SORT_DESC]);
        $mainQuery->where(['IN', 'account_movement.account_movement_id', $query]);

        $dataProvider = new ActiveDataProvider([
            'query' => $mainQuery,
            'key'=> 'account_movement_item_id'
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if($this->account_id) {
            $account = Account::findOne($this->account_id);
            $query->andWhere(['>=', 'account.lft', $account->lft])
                ->andWhere(['<=', 'account.rgt', $account->rgt]);
        }

        $query->andFilterWhere(['company_id' => $this->company_id]);

        //Estado/s de factura
        $this->filterStatus($query);

        //Fechas
        $this->filterDates($query);

        $this->filterBalance($query);

        $query->andFilterWhere(['account_movement.account_movement_id' => $this->account_movement_id]);

        // Totales
        $queryTotals = clone $mainQuery;
        $queryTotals->select(['sum(coalesce(ami.debit,0)) as debit', 'sum(coalesce(ami.credit,0)) as credit']);
        $queryTotals->groupBy("");
        $rsTotals = $queryTotals->one();

        $this->totalDebit = $rsTotals['debit'];
        $this->totalCredit = $rsTotals['credit'];

        if ($mode == 0) {
            return $dataProvider;
        }else{
            return $mainQuery->all();
        }
        
    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterStatus($query, $alias = 'account_movement') {

        if (!empty($this->statuses)) {
            $query->andFilterWhere([
                "$alias.status" => $this->statuses,
            ]);
        } else {

            $query->andFilterWhere([
                "$alias.status" => $this->status,
            ]);
        }
    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterDates($query, $alias = null, $all= false) {
        if (isset($this->date) && !empty($this->date) && (!isset($this->fromDate) && !isset($this->toDate) )) {
            if ($alias != null) {
                $query->andFilterWhere([$alias . '.date' => Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')]);
            } else {
                $query->andFilterWhere(['account_movement.date' => Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')]);
            }
        } else {
            if (empty($this->fromDate) && !$all) {
                $this->fromDate = (new \DateTime('first day of this month'))->format('d-m-Y');
            }

            if (empty($this->toDate) && !$all) {
                $this->toDate = (new \DateTime('last day of this month'))->format('d-m-Y');
            }
            if ($alias != null) {
                if (!empty($this->fromDate)){
                    $query->andFilterWhere(['>=', $alias . '.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);
                }

                if (!empty(($this->toDate))) {
                    $query->andFilterWhere(['<=', $alias . '.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
                }
            } else {
                $query->andFilterWhere(['>=', 'account_movement.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);
                $query->andFilterWhere(['<=', 'account_movement.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
            }
        }
    }

    /**
     * Agrega queries para filtrar por tiempo
     * @param type $query
     */
    private function filterTimes($query, $alias = null)
    {
        $table = $alias ? $alias : 'account_movement';
        if ($this->fromTime) {
            $query->andFilterWhere(['>', "$table.time", $this->fromTime]);
        }

        if ($this->toTime) {
            $query->andFilterWhere(['<=', "$table.time", $this->toTime]);
        }
    }

    /*
     * Aplica el filtro desde una fecha y hora especificas hasya una fecha y hora especificas
     * Los filtro filterDates y filterTimes si se usan juntos generan un comportamiento inesperado
     */
    private function filterDatetime($query, $alias = 'account_movement') {
        if ($this->fromDatetime && empty($this->fromDate) && empty($this->toDate) && empty($this->fromTime) && empty($this->toTime)){
            $query->andFilterWhere(['>', "UNIX_TIMESTAMP(concat($alias.date, ' ', $alias.time))", strtotime($this->fromDatetime)]);
        }

        if ($this->toDatetime && empty($this->fromDate) && empty($this->toDate) && empty($this->fromTime) && empty($this->toTime)){
            $query->andFilterWhere(['<=', "UNIX_TIMESTAMP(concat($alias.date, ' ', $alias.time))", strtotime($this->toDatetime)]);
        }
    }

    private function filterBalance($query) {
        if (!empty($this->balance)) {
            if ($this->balance == 'credit') {
                $query->andFilterWhere(['>', 'ami.credit', 0]);
            } else if ($this->balance == 'debit') {
                $query->andFilterWhere(['>', 'ami.debit', 0]);
            }
        }
    }

    public function statusAccount($all= false) {
        if(isset($this->fromDate)){
            $subQuery = (new Query())
                    ->select([
                        'IF(isnull(ami.debit), 0, ami.debit) as debit',
                        'IF(isnull(ami.credit), 0, ami.credit) as credit',
                    ])
                    ->from('account_movement am')
                    ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
                    ->leftJoin('account ac', 'ami.account_id = ac.account_id')
                    ->where('ac.lft between ' . $this->account_id_from . ' and ' . $this->account_id_to)
                    ->andFilterWhere(['<'.($all?'=':''), 'am.date', Yii::$app->formatter->asDate(($all ? (new \DateTime('now'))->format('d-m-Y') : $this->fromDate), 'yyyy-MM-dd')])
                    ->groupBy(['am.date', 'ami.account_movement_item_id', 'ami.status'])
                    ->orderBy('am.date');

            $queryTotals = new Query();
            $queryTotals->select(['sum(c.debit) as debit', 'sum(c.credit) as credit']);
            $queryTotals->from(['c' => $subQuery]);

            $rsTotals = $queryTotals->one();
            if ($rsTotals['debit'] !== null && $rsTotals['credit'] !== null) {
                return ['debit' => $rsTotals['debit'], 'credit' => $rsTotals['credit'], 'balance' => ($rsTotals['debit'] - $rsTotals['credit'])];
            } else {
                return ['debit' => 0, 'credit' => 0, 'balance' => 0];
            }
        }else{
            $subQuery = (new Query())
                    ->select([
                        'IF(isnull(ami.debit), 0, ami.debit) as debit',
                        'IF(isnull(ami.credit), 0, ami.credit) as credit',
                    ])
                    ->from('account_movement am')
                    ->leftJoin('account_movement_item ami', 'am.account_movement_id = ami.account_movement_id')
                    ->leftJoin('account ac', 'ami.account_id = ac.account_id')
                    ->where('ac.lft between ' . $this->account_id_from . ' and ' . $this->account_id_to)
                    ->andFilterWhere(['<', 'am.date', Yii::$app->formatter->asDate((isset($this->date)? $this->date : $this->initStatusDate), 'yyyy-MM-dd')])
                    ->groupBy(['am.date', 'ami.account_movement_item_id', 'ami.status'])
                    ->orderBy('am.date');

            $queryTotals = new Query();
            $queryTotals->select(['sum(c.debit) as debit', 'sum(c.credit) as credit']);
            $queryTotals->from(['c' => $subQuery]);
            $rsTotals = $queryTotals->one();
            if ($rsTotals['debit'] !== null && $rsTotals['credit'] !== null) {
                return ['debit' => $rsTotals['debit'], 'credit' => $rsTotals['credit'], 'balance' => ($rsTotals['debit'] - $rsTotals['credit'])];
            } else {
                return ['debit' => 0, 'credit' => 0, 'balance' => 0];
            }
        }
    }

    public function filterByCustomerDocumentNumber($filter, $models)
    {
        $movements = [];

        foreach ($models as $model) {
            $customer = AccountMovement::searchCustomer($model['account_movement_id']);
            $profileClass = \app\modules\sale\models\ProfileClass::findOne(['name' => 'cuit2']);
            $cuit2= '';
            if ($customer) {

                if ($profileClass) {
                    $cuit2 = $customer->getProfile($profileClass->profile_class_id);
                }

                if ($customer->document_number === $filter || $cuit2 === $filter) {
                    array_push($movements, $model);
                }

            }
        }

        return $movements;
    }

    public function searchMayorBook($params)
    {
        $this->load($params);

        $initBalance = 0;
        //Si viene el filtro de fechas calculamos el saldo inicial
        if ($this->fromDate) {
            Yii::$app->db->createCommand('set @init_balance := 0')->execute();
            $initBalanceQuery = (new Query())
                ->select(['@init_balance := @init_balance + (coalesce(ami.debit,0) - coalesce(ami.credit,0)) as init_balance'])
                ->from('account_movement_item ami')
                ->innerJoin('account_movement am', 'am.account_movement_id=ami.account_movement_id')
                ->andWhere(['ami.account_id' => $this->account_id])
                ->andWhere(['<', 'am.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')])
                ->orderBy(['am.date' => SORT_ASC, 'am.time' => SORT_ASC]);

            $initBalanceResult = $initBalanceQuery->all();
            if (!empty($initBalanceResult)) {
                $initBalance = end($initBalanceResult)['init_balance'];
            }
        }

        Yii::$app->db->createCommand('set @balance := '.$initBalance)->execute();

        $query = (new Query())
            ->select(['ami.account_movement_id', 'ami.debit', 'ami.credit', 'ami.status', 'am.description',
                '@balance := @balance + (coalesce(ami.debit,0) - coalesce(ami.credit, 0)) as balance',
                'am.date'
            ])
            ->from('account_movement_item ami')
            ->innerJoin('account_movement am', 'am.account_movement_id=ami.account_movement_id');

        if ($this->fromDate) {
            $query->andWhere(['>=', 'am.date', Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd')]);
        }

        if ($this->toDate) {
            $query->andWhere(['<=', 'am.date', Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd')]);
        }

        $query->andWhere(['ami.account_id' => $this->account_id]);

        $query->orderBy(['am.date' => SORT_ASC, 'am.time' => SORT_ASC]);

        return $query;

    }

}
