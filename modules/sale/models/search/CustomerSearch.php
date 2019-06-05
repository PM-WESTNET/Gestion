<?php

namespace app\modules\sale\models\search;

use app\components\db\BigDataProvider;
use app\components\helpers\SearchStringHelper;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerHasClass;
use app\modules\ticket\models\Ticket;
use DateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * CustomerSearch represents the model behind the search form about app\modules\sale\models\Customer.
 */
class CustomerSearch extends Customer {

    public $search_text;
    public $toDate;
    public $fromDate;
    public $debt_bills;
    public $debt_bills_from;
    public $debt_bills_to;
    public $payed_bills;
    public $payed_bills_from;
    public $payed_bills_to;
    public $total_bills;
    public $total_bills_from;
    public $total_bills_to;
    public $zone_id;
    public $customer_class_id;
    public $customer_category_id;
    public $connection_status;
    public $contract_status;
    public $contract_min_age;
    public $contract_max_age;
    public $activatedFrom;
    public $customers_id;
    public $customer_id;

    //TODO: dejar solo nodes
    public $node_id;
    public $nodes = [];
    
    public $company_id;
    
    public $plan_id;
    
    public $customer_number;
    public $customer_status;
    public $amount_due;
    public $amount_due_to;
    public $geocode;

    //Email status
    public $email_status;
    public $email2_status;

    public function rules()
    {
        return [
            [['customer_id', 'document_type_id', 'debt_bills', 'plan_id'], 'integer'],
            [['name', 'lastname', 'document_number', 'sex', 'email', 'phone',  'status', 'debt_bills', 'debt_bills_from','debt_bills_to'], 'safe'],
            [['payed_bills',  'payed_bills_from','payed_bills_to', 'total_bills', 'total_bills_from','total_bills_to',  'contract_status'],'safe'],
            [['nodes', 'amount_due_to', 'geocode', 'search_text', 'toDate', 'fromDate', 'zone_id', 'customer_class_id', 'amount_due'],'safe'],
            [['customer_category_id', 'connection_status', 'node_id', 'company_id', 'customer_number', 'customer_status', 'amount_due_to'], 'safe'],
            [['contract_min_age', 'contract_max_age', 'activatedFrom', 'customers_id'], 'safe'],
            [['email_status', 'email2_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'toDate' => Yii::t('app', 'To Date'),
            'fromDate' => Yii::t('app', 'From Date'),
            'activatedFrom' => Yii::t('app', 'Activated From'),
            'debt_bills' => Yii::t('app', 'Debt Bills'),
            'debt_bills_from' => Yii::t('app', 'Debt Bills From'),
            'debt_bills_to' => Yii::t('app', 'Debt Bills To'),
            'zone_id' => Yii::t('app', 'Zone'),
            'customer_class_id' => Yii::t('app', 'Customer Class'),
            'customer_category_id'=> Yii::t('app', 'Customer Category'),
            'connection_status' => Yii::t('app', 'Connection Status'),
            'contract_status' => Yii::t('app', 'Contract Status'),
            'node_id' => Yii::t('app', 'Node ID'),
            'company_id' => Yii::t('app', 'Company'),
            'plan_id' => Yii::t('app', 'Plan'),
            'customer_status' => \Yii::t('app', 'Customer Status'),
            'customer_number' => Yii::t('app', 'Customer Number'),
            'amount_due' => Yii::t('app', 'Amount due'),
            'status_account' => Yii::t('app', 'Connection Status'),
            'customer_id' => Yii::t('app', 'Customer'),
            'geocode' => Yii::t('westnet', 'Geocode'),
            'email_status' => Yii::t('app', 'Email 1 status'),
            'email2_status' => Yii::t('app', 'Email 2 status'),

        ]);
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Busqueda para filtros
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->buildSearchQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
    
    public function searchForExport($params, $normal=true)
    {
        $subQueryClass = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_class_has_customer')
            ->groupBy(['customer_class_has_customer.customer_id']);

        $subQueryCategory = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_category_has_customer')
            ->groupBy(['customer_category_has_customer.customer_id']);

        $query = (new Query());
        $query->distinct()
            ->select(['customer.code', 'CONCAT(customer.lastname, ", ",customer.name) as name', 'customer.document_number', 'customer.phone','customer.phone2', 'customer.phone3', 'customer.phone4', 'customer.email', 'customer.email2', 'cc.name as class', 'ccat.name as category', 'company.name as company'])
            ->select(['customer.code', 'CONCAT(customer.lastname, ", ",customer.name) as name', 'customer.document_number', 'customer.phone','customer.phone2', 'customer.phone3', 'customer.phone4', 'customer.email', 'customer.email2', 'cc.name as class', 'ccat.name as category', 'company.name as company'])
            ->from('customer')
            ->leftJoin('address add', 'add.address_id = customer.address_id' )
            ->innerJoin('customer_class_has_customer cchc', 'cchc.customer_id= customer.customer_id')
            ->innerJoin(['cchc2'=> $subQueryClass], 'cchc2.customer_id = customer.customer_id and cchc.date_updated = cchc2.maxdate')
            ->innerJoin('customer_category_has_customer ccathc', 'ccathc.customer_id = customer.customer_id')
            ->innerJoin(['ccathc2'=> $subQueryCategory], 'ccathc2.customer_id = customer.customer_id and ccathc.date_updated = ccathc2.maxdate')
            ->leftJoin('contract', 'contract.customer_id = customer.customer_id')
            ->leftJoin('contract_detail', 'contract_detail.contract_id = contract.contract_id')
            ->leftJoin('connection', 'connection.contract_id= contract.contract_id')
            ->leftJoin('company', 'company.company_id= customer.company_id')
            ->leftJoin('customer_class cc', 'cc.customer_class_id= cchc.customer_class_id')
            ->leftJoin('customer_category ccat', 'ccat.customer_category_id= ccathc.customer_category_id')
            ;

        $this->load($params);
        
        if($normal){
            $this->filterByCategory($query);
            $this->filterByClass($query);
        }else{
            $this->filterByCategories($query);
            $this->filterByClasses($query);
        }
        $this->filterByCompany($query);
        //$this->filterByConnectionStatus($query);
        $this->filterByStatusAccount($query);
        $this->filterByContractStatus($query);
        $this->filterByContractAge($query);
        $this->filterByNode($query);
        $this->filterByNodes($query);
        $this->filterByZone($query);
        $this->filterByPlan($query);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'document_number', $this->document_number])
            ->andFilterWhere(['customer.code' => $this->customer_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->groupBy(['customer.customer_id']);
        
        if (!empty($this->customer_status)) {
            $query->andWhere(['customer.status' => $this->customer_status]);
        }
        
        return $query;
    }
    
    /**
     * Genera un query para busqueda de customers
     * @param array $params
     * @return ActiveQuery
     */
    public function buildSearchQuery($params, $normal = true)
    {
        $subQueryClass = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_class_has_customer')
            ->groupBy(['customer_id']);

        $subQueryCategory = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_category_has_customer')
            ->groupBy(['customer_id']);

        $query = Customer::find();
        $query->distinct()
            ->leftJoin('address add', 'add.address_id = customer.address_id' )
            ->innerJoin('customer_class_has_customer cchc', 'cchc.customer_id= customer.customer_id')
            ->innerJoin(['cchc2'=> $subQueryClass], 'cchc2.customer_id = customer.customer_id and cchc.date_updated = cchc2.maxdate')
            ->innerJoin('customer_category_has_customer ccathc', 'ccathc.customer_id = customer.customer_id')
            ->innerJoin(['ccathc2'=> $subQueryCategory], 'ccathc2.customer_id = customer.customer_id and ccathc.date_updated = ccathc2.maxdate')
            ->leftJoin('contract', 'contract.customer_id = customer.customer_id')
            ->leftJoin('contract_detail', 'contract_detail.contract_id = contract.contract_id')
            ->leftJoin('connection', 'connection.contract_id= contract.contract_id')
            ;

        $this->load($params);

        if($normal){
            $this->filterByCategory($query);
            $this->filterByClass($query);
        }else{
            $this->filterByCategories($query);
            $this->filterByClasses($query);
        }
        $this->filterByCompany($query);
        //$this->filterByConnectionStatus($query);
        $this->filterByStatusAccount($query);
        $this->filterByContractStatus($query);
        $this->filterByContractAge($query);
        $this->filterByNode($query);
        $this->filterByNodes($query);
        $this->filterByZone($query);
        $this->filterByPlan($query);
        $this->filterByIssetGeocode($query);
        $this->filterEmailStatus($query);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'document_number', $this->document_number])
            ->andFilterWhere(['customer.code' => $this->customer_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['customer.customer_id' => $this->customer_id])
            ->groupBy(['customer.customer_id']);
        
        if (!empty($this->customer_status)) {
            $query->andWhere(['customer.status' => $this->customer_status]);
        }
        
        return $query;
    }

    /**
     * Busqueda con like (searchFlex busca con or like)
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchText($params)
    {

        $query = Customer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['customer_id'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
          return $dataProvider;
        }

        $searchHelper = new SearchStringHelper();
        $searchHelper->string = $this->search_text;

        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');

        $operator = 'like';

        $query->where([$operator,'customer.name',$words,false])
            ->orWhere([$operator,'lastname',$words,false])
            ->orWhere([$operator,'email',$words,false])
            ->orWhere([$operator,'customer.code',$words,false])
            ->orWhere([$operator,'document_number',$words,false])
            ->orWhere([$operator,'phone',$words,false]);

        //Busqueda en profiles
        $query->joinWith('customerProfiles', false);


        //Profiles habilitados para busqueda
        $profileClasses = Customer::getSearchableProfileClasses();
        foreach($profileClasses as $class){

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach($words as $word)
                $query->orWhere('profile.value LIKE :word',[':word'=>$word]);
        
        }

        return $dataProvider;
        
    }

        
    public static function getdataProviderClasses($customer_id = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CustomerHasClass::find()->where(['customer_id'=>$customer_id])->orderBy(['date_updated'=>SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    /**
     * Busqueda con "or like"
     * @param type $params
     * @return ActiveDataProvider
     */
    public function searchFlex($params = null){

        $query = Customer::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['customer_id'=>SORT_DESC]
            ]
        ]);

        if ($params != null && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $searchHelper = new SearchStringHelper();
        $searchHelper->string = $this->search_text;

        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');

        $operator = 'like';

        $query->where([$operator,'customer.name',$words,false])
            ->orWhere([$operator,'lastname',$words,false])
            ->orWhere([$operator,'code',$words,false])
            ->orWhere([$operator,'email',$words,false])
            ->orWhere([$operator,'document_number',$words,false])
            ->orWhere([$operator,'phone',$words,false]);


        //Busqueda en profiles
        $query->joinWith('customerProfiles', false);

        //Profiles habilitados para busqueda
        $profileClasses = Customer::getSearchableProfileClasses();
        foreach($profileClasses as $class){

            /* El query debe ser armado asi para que funcione coorectamente. Pasando profile_class_id como parametro :profile_class_id no funciona.
             * Utilizando llamadas a orWhere o a sus variantes, no se concatena adecuadamente. Con LIKE no es posible agragar un array con el
             * query de la porcion AND porque es ignorado por Query */
            foreach($words as $word)
                $query->orWhere('profile.value LIKE :word',[':word'=>$word]);
        
        }

        return $dataProvider;
        
    }

    /**
     * Busqueda de deudores
     * @param array $params
     * @return BigDataProvider
     */
    public function searchDebtors($params, $pageSize = 20)
    {
        if(array_key_exists('CustomerSearch', $params)===false) {
            $params['CustomerSearch']['amount_due'] = 0;
        }
        $query = $this->buildDebtorsQuery($params);

        if (isset(Yii::$app->session)) {
            Yii::$app->session->set('totalDebtors', $query->sum('saldo'));
        }

        $dataProvider = new BigDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => (isset($params['page']) ? $params['page'] -1 : 0 )
            ],
        ]);
        
        return $dataProvider;

    }
    
    /**
     * Genera un query para buscar a los deudores
     * @param array $params
     * @return Query
     */
    public function buildDebtorsQuery($params)
    {
        
        $this->load($params);

        $queryBill = (new Query())
            ->select(['customer_id', 'b.date as date', new Expression('if(bt.multiplier<0, 0,1) AS i'),
                new Expression('sum(b.total * bt.multiplier) AS amount')])
            ->from(new Expression('bill b FORCE INDEX(fk_bill_customer1_idx)'))
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id' )
            ->where(['<>','b.status','draft'])
            ->andWhere(['<>','b.total',0])
            ->groupBy(['b.customer_id','b.bill_id'])
        ;

        $subQueryClass = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_class_has_customer')
            ->groupBy(['customer_id']);
        
        $subQueryCategory = (new Query())
            ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
            ->from('customer_category_has_customer')
            ->groupBy(['customer_id']);

        $queryPayment = (new Query())
            ->select(['p.customer_id', 'p.date as date', new Expression('0 AS i'), new Expression('-p.amount')])
            ->from('payment as p')
        ;
        if (!empty($this->toDate)){
            $queryBill->andWhere(['<=', 'b.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd') ]);
            $queryPayment->andWhere(['<=', 'p.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd')]);
        }

        $qty_query = "(SELECT customer_id, sum(qty) as debt_bills, sum(qty_2) AS payed_bills " .
            "FROM ( " .
            "    SELECT customer_id, date, i, round(amount,2), " .
            "        @saldo:=round(if(customer_id<>@customer_ant and @customer_ant <> 0, amount, @saldo + amount ),2) as saldo, " .
            "        @customer_ant:=customer_id, " .
            "        if((@saldo - (select cc.percentage_tolerance_debt from customer_class_has_customer cchc
         INNER JOIN (SELECT customer_id, max(date_updated) maxdate FROM customer_class_has_customer GROUP BY customer_id) cchc2 ON cchc2.customer_id = cchc.customer_id AND cchc.date_updated = cchc2.maxdate
         LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id  where cchc.customer_id =a.customer_id)) > 0 and i=1, 1, 0) as qty, " .
            "        if(@saldo <= 0 AND i = 1, 1, 0) as qty_2 " .
            "    FROM (".$queryBill->union($queryPayment,true)->createCommand()->getRawSql() .
            "    ) a order by customer_id, i, date " .
            ") a " .
            "GROUP BY customer_id ) " ;

        $qMethodPayment = (new Query())->select(['payment_method_id'])
                ->from('payment_method')
                ->where("type='account'");


        $subQueryBills = (new Query())
            ->select(['sum(b.total * bt.multiplier) as amount'])
            ->from('bill b')
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
            ->where("b.status <> 'draft' and b.customer_id = customer.customer_id");

        if (!empty($this->toDate)){
            $subQueryBills->andWhere(['<=', 'b.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd') ]);
        }

        $subQueryPayments = (new Query())
            ->select(['sum(pi.amount)'])
            ->from('payment p')
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id and pi.payment_method_id NOT IN('.$qMethodPayment->createCommand()->getSql().')')
            ->where("(p.status <> 'cancelled' and p.status <> 'draft') and p.customer_id = customer.customer_id");

        if (!empty($this->toDate)){
            $subQueryPayments->andWhere(['<=', 'p.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd') ]);
        }
        
        if (!empty($this->fromDate)){
            $subQueryPayments->andWhere(['>=', 'p.date', Yii::$app->getFormatter()->asDate($this->fromDate, 'yyyy-MM-dd') ]);
        }

        $masterSubQuery = (new Query())->select(['customer.customer_id', 'concat(customer.lastname, \' \', customer.name) as name', 'customer.phone', 'customer.code',
                'round(coalesce(('.$subQueryBills->createCommand()->getRawSql().'), 0) - coalesce(('.$subQueryPayments->createCommand()->getRawSql().'), 0)) as saldo', 'bills.debt_bills', 'bills.payed_bills',
                new Expression('( bills.debt_bills +  bills.payed_bills) as total_bills'),
            'contract_detail.product_id as plan',
            'customer.company_id as customer_company'
            ])
            ->from('customer')
            ->leftJoin(['bills'=>$qty_query], 'bills.customer_id = customer.customer_id')
            ->leftJoin('contract', 'contract.customer_id = customer.customer_id')
            ->leftJoin('contract_detail', 'contract.contract_id = contract_detail.contract_id')

            //Para clase de cliente
            ->innerJoin('customer_class_has_customer cchc', 'cchc.customer_id= customer.customer_id')
            ->innerJoin(['cchc2'=> $subQueryClass], 'cchc2.customer_id = customer.customer_id and cchc.date_updated = cchc2.maxdate')
            ->innerJoin('customer_category_has_customer ccathc', 'ccathc.customer_id= customer.customer_id')
            ->innerJoin(['ccathc2'=> $subQueryCategory], 'ccathc2.customer_id = customer.customer_id and ccathc.date_updated = ccathc2.maxdate')    
            ->leftJoin('customer_class as cc', 'cchc.customer_class_id = cc.customer_class_id')
                ->leftJoin('customer_category as ccat', 'ccathc.customer_category_id = ccat.customer_category_id')
            
            //Para Nodes
            ->leftJoin('connection', 'connection.contract_id = contract.contract_id')
            ->leftJoin('node n', 'connection.node_id = n.node_id')

            ->groupBy(['customer.customer_id', 'customer.name', 'customer.phone']);

        // Si la empresa por la que filtro, es una de las padres, hago la relacion con el parent_company
        if(($this->company) && $this->company->parent_id) {
            $masterSubQuery->leftJoin('company', 'company.company_id = customer.company_id');
            $this->filterByCompany($masterSubQuery);
        } else {
            $masterSubQuery->leftJoin('company', 'company.company_id = customer.parent_company_id');
            $this->filterByCompany($masterSubQuery, true);
        }


        $this->filterByClasses($masterSubQuery);
        $this->filterByCategories($masterSubQuery);

        $this->filterByNodes($masterSubQuery);
        $this->filterByContractStatus($masterSubQuery);
        $this->filterByContractAge($masterSubQuery);
        $this->filterByPlan($masterSubQuery);
        $this->filterByCustomer($masterSubQuery);

        if (!empty($this->activatedFrom)){
            $masterSubQuery->andWhere(['>=', 'contract.from_date', Yii::$app->getFormatter()->asDate($this->activatedFrom, 'yyyy-MM-dd') ]);
        }
        $masterSubQuery->andFilterWhere(['customer.code' => $this->customer_number]);
        
        $masterSubQuery->andFilterWhere(['like', 'customer.name', $this->name]);


        Yii::$app->db->createCommand('set @saldo := 0; set @customer_ant= 0;')->execute();
        $query = (new Query())
                ->select([new Expression('SQL_CALC_FOUND_ROWS *')])
            ->from(['b'=>$masterSubQuery]);

        if($this->debt_bills > 0) {
            $query->andWhere(['=', 'debt_bills', $this->debt_bills ]);
        }

        if(!is_null($this->amount_due)) {
            $query->andFilterWhere(['>', 'saldo', (double)$this->amount_due]);
        }

        if(!is_null($this->amount_due_to)) {
            $query->andWhere(['<', 'saldo', $this->amount_due_to]);
        }


        if($this->debt_bills_from > 0) {
            $query->andWhere(['>=', 'debt_bills', $this->debt_bills_from ]);
        }

        if ($this->debt_bills_to > 0) {
            $query->andWhere(['<=', 'debt_bills', $this->debt_bills_to]);
        }

        if ($this->payed_bills_from > 0) {
            $query->andWhere(['>=', 'payed_bills', $this->payed_bills_from]);
        }

        if ($this->payed_bills_to > 0) {
            $query->andWhere(['<=', 'payed_bills', $this->payed_bills_to]);
        }

        if ($this->total_bills_from > 0) {
            $query->andWhere(['>=', 'total_bills', $this->total_bills_from]);
        }

        if ($this->total_bills_to > 0) {
            $query->andWhere(['<=', 'total_bills', $this->total_bills_to]);
        }

        $masterSubQuery->andFilterWhere(['customer.status' => $this->customer_status]);

        return $query;
        
    }
    
   

    public function searchByName($name)
    {
        /** @var Query $query */
        $query = Customer::find();

        $searchHelper = new SearchStringHelper();
        $searchHelper->string = $name;
        $words = $searchHelper->getSearchWords('%{word}%');
        $operator = 'like';
        $query
            ->where([$operator, "CONCAT(customer.code, ' - ', lastname, ' ', customer.name )",$words,false])
            ->orderBy(['customer.code' => SORT_ASC, 'lastname'=>SORT_ASC, 'customer.name'=>SORT_ASC]);

        return $query;
    }

    public function searchByNameAndCompany($name, $company_id)
    {
        /** @var Query $query */
        $company = Company::findOne($company_id);

        $query = Customer::find();

        $searchHelper = new SearchStringHelper();
        $searchHelper->string = $name;
        $words = $searchHelper->getSearchWords('%{word}%');
        $operator = 'like';
        $query->where([$operator, "CONCAT(customer.code, ' - ', lastname, ' ', customer.name )",$words,false]);

        if($company->parent_id) {
            $query->andWhere(['customer.company_id' => $company_id]);
        } else {
            $query->andWhere(['customer.parent_company_id' => $company_id]);
        }

        $query->orderBy(['lastname'=>SORT_ASC, 'customer.name'=>SORT_ASC]);

        return $query;
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
    
     private function filterByClasses($query){
        if (!empty($this->customer_class_id)) {
            $query->andFilterWhere(['cchc.customer_class_id' => $this->customer_class_id]);
        }
    }

    private function filterByCategories ($query){
        if (!empty($this->customer_category_id)) {
            $query->andFilterWhere(['ccathc.customer_category_id' => $this->customer_category_id]);
        }
    }
    
    private function filterByConnectionStatus($query){
        if (!empty($this->connection_status)) {
            $query->andWhere(['connection.status' => $this->connection_status]);
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
    }

    private function filterByContractAge($query) {
        if (!empty($this->contract_min_age) || !empty($this->contract_max_age)) {
            $query->leftJoin('product', 'product.product_id = contract_detail.product_id');
            $query->andWhere(['product.type' => 'plan']);
        }

        if (!empty($this->contract_min_age)) {
            $fromDate = new DateTime("today -$this->contract_min_age months");
            $query->andWhere(['<', 'contract_detail.from_date', $fromDate->format('Y-m-d')]);
        }

        if (!empty($this->contract_max_age)) {
            $fromDate = new DateTime("today -$this->contract_max_age months");
            $query->andWhere(['>', 'contract_detail.from_date', $fromDate->format('Y-m-d')]);
        }
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

    private function filterByCustomer($query){
        if (!empty($this->customers_id)) {
            $query->andWhere(['customer.customer_id' => $this->customers_id]);
        }
    }

    private function filterByIssetGeocode($query)
    {
        $determinant = 0;
        if (!empty($this->geocode)) {
            foreach ($this->geocode as $key) {
                $determinant = $determinant + $key;
                }
        if ($determinant == 1) {
            $query->andFilterWhere(['<>', 'add.geocode', '-32.8988839,-68.8194614']);
        }
        if ($determinant == 2) {
            $query->andFilterWhere(['add.geocode' => '-32.8988839,-68.8194614']);
        }
        }
    }

    private function filterEmailStatus($query){
        if ($this->email_status) {
            $query->andWhere(['customer.email_status' => $this->email_status]);
        }

        if ($this->email2_status) {
            $query->andWhere(['customer.email2_status' => $this->email2_status]);
        }
    }

    public function searchDebtBills($customer_id)
    {
        Yii::$app->db->createCommand('set @saldo := 0; set @customer_ant= 0;')->execute();

        $queryBill = (new Query())
            ->select(['customer_id', 'b.date as date', new Expression('if(bt.multiplier<0, 0,1) AS i'),
                new Expression('sum(b.total * bt.multiplier) AS amount')])
            ->from(new Expression('bill b FORCE INDEX(fk_bill_customer1_idx)'))
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id' )
            ->where(['b.status'=>['closed', 'completed'], 'b.customer_id'=>$customer_id])
            ->groupBy(['b.customer_id','b.bill_id'])
        ;

        $queryPayment = (new Query())
            ->select(['p.customer_id', 'p.date as date', new Expression('0 AS i'), new Expression('-p.amount')])
            ->from('payment as p')
            ->where(['p.status'=>'closed', 'p.customer_id'=>$customer_id])
        ;

        $subQuery = (new Query());
        $subQuery->select(['customer_id', 'date','i', 'round(amount, 2)',new Expression('@saldo:=round(if(customer_id<>@customer_ant and @customer_ant <> 0, amount, @saldo + amount ),2)  as saldo'),
            new Expression('@customer_ant:=customer_id'),
            new Expression('if((@saldo - (select cc.percentage_tolerance_debt from customer_class_has_customer cchc
         INNER JOIN (SELECT customer_id, max(date_updated) maxdate FROM customer_class_has_customer GROUP BY customer_id) cchc2 ON cchc2.customer_id = cchc.customer_id AND cchc.date_updated = cchc2.maxdate
         LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id  where cchc.customer_id =a.customer_id)) > 0 and i=1, 1, 0) as qty'),
            new Expression('if(@saldo <= 0 AND i = 1, 1, 0) as qty_2') ])
            ->from(['a'=> $queryBill->union($queryPayment,true) ])
            ->orderBy(['customer_id'=>SORT_ASC, 'i'=>SORT_ASC, 'date'=>SORT_ASC])
        ;

        $mainQuery = (new Query());
        $mainQuery
            ->select(["customer_id",  "sum(qty) as debt_bills", "sum(qty_2) AS payed_bills"])
            ->from(['a'=>$subQuery])
            ->where(['customer_id'=>$customer_id])
            ->groupBy(['customer_id'])
        ;
        //error_log(print_r($mainQuery->createCommand()->getRawSql(),1));
        return $mainQuery->one();
    }
    
    public function searchAllBills(){
        
         Yii::$app->db->createCommand('set @saldo := 0; set @customer_ant= 0;')->execute();
        $queryBill = (new Query())
            ->select(['customer_id', 'b.date as date', new Expression('if(bt.multiplier<0, 0,1) AS i'),
                new Expression('sum(b.total * bt.multiplier) AS amount')])
            ->from(new Expression('bill b FORCE INDEX(fk_bill_customer1_idx)'))
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id' )
            ->where(['b.status'=>'closed',])
            ->groupBy(['b.customer_id','b.bill_id'])
        ;

        $queryPayment = (new Query())
            ->select(['p.customer_id', 'p.date as date', new Expression('0 AS i'), new Expression('-p.amount')])
            ->from('payment as p')
            ->where(['p.status'=>'closed'])
        ;
        
        $subQuery = (new Query());
        $subQuery->select(['*',new Expression('@saldo:=round(if(customer_id<>@customer_ant and @customer_ant <> 0, amount, @saldo + amount ))  as saldo'),
            new Expression('@customer_ant:=customer_id'),
            new Expression('if(i>0,1,0) AS qty'),])
            ->from(['a'=> $queryBill->union($queryPayment,true) ])
            ->orderBy(['customer_id'=>SORT_ASC, 'i'=>SORT_ASC, 'date'=>SORT_ASC])
            
        ;
        
        

              
        $mainQuery = (new Query());
        $mainQuery
            ->select(["customer_id",  "sum(if(qty>0, 1, 0)) as bills"])
            ->from(['a'=>$subQuery])
            
            ->groupBy(['customer_id'])
            
        ;
        return $mainQuery;
    }
    
    public function getTicketsCount()
    {
        return Ticket::find()->where(['customer_id' => 'contract.customer_id']);
    }
}
