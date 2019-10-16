<?php

namespace app\modules\checkout\models\search;

use app\modules\sale\models\Bill;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\checkout\models\Payment;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\db\QueryBuilder;

/**
 * PaymentSearch represents the model behind the search form about `app\modules\checkout\models\Payment`.
 */
class PaymentSearch extends Payment
{
    public $payment_method;
    public $bill_numbers;
    public $company_name;
    public $type;
    public $name;
    public $saldo;
    public $customer_number;
    public $company_id;
    public $customer_name;
    public $customer_lastname;
    public $from_date;
    public $to_date;
    public $_status;
    public $from_amount;
    public $to_amount;

    public $from;
    public $to;

    public $paymentMethods;
    public $only_closed_bills;
    public $only_closed_payments;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'timestamp', 'customer_id'], 'integer'],
            [['amount'], 'number'],
            [['from', 'to'], 'date'],
            [[
                'date',
                'time',
                'concept',
                'number',
                'from',
                'to',
                'type',
                'customer_number',
                'company_id',
                'customer_name',
                'customer_lastname',
                'from_date',
                'to_date',
                '_status',
                'from_amount',
                'to_amount',
                'paymentMethods',
                'only_closed_bills',
                'only_closed_payments'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels = array_merge($labels, [
            'to' => Yii::t('app', 'To'),
            'from' => Yii::t('app', 'From'),
            'customer_number' => \Yii::t('app', 'Customer Number'),
            'company_id' => \Yii::t('app', 'Company'),
            'customer_name'=> \Yii::t('app', 'Name'),
            'customer_lastname' => \Yii::t('app', 'Lastname'),
            'from_date' => \Yii::t('app', 'From Date'),
            'to_date'=> \Yii::t('app', 'To Date'),
            '_status'=> \Yii::t('app', 'Status'),
            'from_amount' => \Yii::t('app', 'From Amount'),
            'to_amount' => \Yii::t('app', 'To Amount'),
            'paymentMethods' => Yii::t('app','Payment Methods')
        ]);
                

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    private function searchPayment($params)
    {
        /** @var ActiveQuery $query */
        $query = self::find();

        $select = ['customer.customer_id', 'customer.name','payment.payment_id', 'payment.concept', 'payment.amount', 'payment.date', 'payment.time', 'payment.status',
            'GROUP_CONCAT( distinct pm.name) as payment_method', 'GROUP_CONCAT( distinct coalesce(bill.number, concat(\'Id: \', bill.bill_id))) as bill_numbers'];

        if(Yii::$app->params['companies']['enabled']){
            $select[]= 'company.name as company_name';
        }

        $query->select($select);

        /**if(!empty($params['PaymentSearch']['date'])){
            try{
                $date = Yii::$app->formatter->asDate($params['PaymentSearch']['date'], 'yyyy-MM-dd');
                $query->andFilterWhere([
                    'payment.date' => $date,
                ]);
            }catch(\Exception $ex) {
                $date = null;
            }
        }**/
        /**if(isset($params['PaymentSearch']) && isset($params['PaymentSearch']['amount'])) {
            $params['PaymentSearch']['amount'] = str_replace(',', '.' , $params['PaymentSearch']['amount']);
        }**/
        $this->load($params);
        $query->joinWith(["paymentItems"]);
        $query->leftJoin('payment_method pm', 'pm.payment_method_id = payment_item.payment_method_id ');
        $query->joinWith(["customer"]);
        $query->leftJoin('bill_has_payment bhp', 'bhp.payment_id = payment.payment_id');
        $query->leftJoin('bill', 'bhp.bill_id = bill.bill_id');
        
        

        if(Yii::$app->params['companies']['enabled']){
            $query->leftJoin('company', 'payment.company_id = company.company_id');
        }

        /**if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $query;
        }**/

        $query->andFilterWhere([
            'payment_id' => $this->payment_id,
            'round(payment.amount,2)' => $this->amount,

            'payment.customer_id' => $this->customer_id,
        ]);



        $query->andFilterWhere(['like', 'concept', $this->concept])
            ->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'customer.name', $this->customer_name])
            ->andFilterWhere(['like', 'customer.lastname', $this->customer_lastname])
            ->andFilterWhere(['=', 'customer.code', $this->customer_number])
            ->andFilterWhere(['payment.status'=> $this->_status])
            ->andFilterWhere(['=', 'customer.company_id', $this->company_id])           
            ->andFilterWhere(['>=', 'payment.amount', $this->from_amount])
            ->andFilterWhere(['<=', 'payment.amount', $this->to_amount]);
        
        if (!empty($this->from_date)) {
            $query->andFilterWhere(['>=', 'payment.date', Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd')]);    
        }
        
        if(!empty($this->to_date)){
            $query->andFilterWhere(['<=', 'payment.date', Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd')]);  
        }

        if(!empty($this->paymentMethods)){
            $query->andFilterWhere(['IN', 'pm.payment_method_id', $this->paymentMethods]);
        }
        
        $groupBy = ['customer.customer_id', 'customer.name', 'payment.payment_id', 'payment.amount', 'payment.date', 'payment.status'];
        if(Yii::$app->params['companies']['enabled']){
            $groupBy[] = 'company.name';
        }
        $query->groupBy($groupBy);
        return $query;
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
        $query = $this->searchPayment($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date'=>SORT_DESC, 'time'=>SORT_DESC]
            ]
        ]);
        return $dataProvider;
    }

    public function searchAccount($customer_id, $params)
    {
        $queryPayments = $this->searchPayment($params);
        $this->load($params);

        $queryPayments->select([
            new Expression("'Payment' AS type"), "customer.customer_id", "concat(customer.lastname, ', ', customer.name) AS name",
            new Expression('0 as bill_id'), "payment.payment_id", "payment.date", "payment.time", "payment.number",
            "payment.amount", "payment.status", "GROUP_CONCAT(DISTINCT pm.name) AS payment_method",
            "GROUP_CONCAT(DISTINCT coalesce(bill.number, concat('Nro.: ', bill.bill_id))) AS bill_numbers",
            "company.name AS company_name"
        ]);

        /** @var Query $queryBills */
        $queryBills = Bill::find();
        $queryBills->select([
            "bill_type.name AS type", "bill.customer_id", "concat(customer.lastname, ', ', customer.name) AS name",
            "bill.bill_id", new Expression("0 AS payment_id"), "bill.date", "bill.time", "bill.number",
            new Expression("(bill.total * bill_type.multiplier) as total"),
            "bill.status", new Expression("'' AS payment_method"), new Expression("'' AS bill_numbers"), "company.name AS company_name"
        ]);
        $queryBills->leftJoin("bill_type", 'bill.bill_type_id = bill_type.bill_type_id' );
        $queryBills->leftJoin("customer", "bill.customer_id = customer.customer_id");
        $queryBills->leftJoin("company", "bill.company_id = company.company_id");
        $queryBills->where([
            'bill.customer_id'=>$customer_id,
            'bill_type.multiplier' => [-1,1]
        ]);

        if($this->only_closed_bills) {
            $queryBills->andWhere(['bill.status' => Bill::STATUS_CLOSED]);
        }

        if($this->only_closed_payments) {
            $queryPayments->andWhere(['payment.status' => Payment::PAYMENT_CLOSED]);
        }

        $desde = (new \DateTime('now -800 month'))->format('Y-m').'-01'; // Get bills from begining TODO improve query
        if(!empty($this->from)) {
            $desde = Yii::$app->formatter->asDate($this->from, 'Y-M-dd');
        }
        $hasta =  (new \DateTime(date('Y-m').'-01'))->modify('+1 month -1 day')->format('Y-m-d') ;
        if(!empty($this->to)) {
            $hasta = Yii::$app->formatter->asDate($this->to, 'Y-M-dd');
        }
        $this->from = Yii::$app->formatter->asDate($desde, 'dd-MM-Y');
        $this->to = Yii::$app->formatter->asDate($hasta, 'dd-MM-Y');

        $queryBills->andFilterWhere(['between', 'bill.date', $desde, $hasta]);
        $queryPayments->andFilterWhere(['between', 'payment.date', $desde, $hasta]);

        if($this->type == 'payment') {
            $queryBills->andWhere(['bill.bill_id'=> -1]);
        }
        if($this->type == 'bill') {
            $queryPayments->andWhere(['payment.payment_id'=> -1]);
        }

        $unionQuery = $queryBills->union($queryPayments);
        $query = (new Query())
            ->select(['b.*', new Expression('(@saldo:=@saldo+if(b.bill_id>0, b.total, if(b.status=\'cancelled\', 0, -b.total ))) as saldo2'), new Expression('(select @saldo )as saldo') ])
            ->from(['b'=>$unionQuery])
            ->orderBy(['date' => SORT_ASC, "time" => SORT_ASC]);
        
        $masterQuery = (new Query());
        $masterQuery->select(["b.type","b.customer_id","b.name","b.bill_id","b.payment_id","b.date","b.time","b.number",
            "b.total", "b.status", "b.payment_method", "b.bill_numbers", "b.company_name", "b.saldo", "c.email"]);
        $masterQuery->from(["c"=> 'customer'])
                    ->leftJoin(["b"=> $query], "b.customer_id = c.customer_id")
                    ->where("b.customer_id IS NOT NULL");
        $cantidad = $masterQuery->count("*");

        $masterQuery->orderBy(['b.date' => SORT_DESC, 'b.bill_id' => SORT_DESC]);
        Yii::$app->db->createCommand( 'set @saldo:=0;')->execute();
        $dataProvider = new ActiveDataProvider([
            'query' => $masterQuery,
            'totalCount' => $cantidad
        ]);
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function account($params)
    {
        $query = Payment::find();

        $query->where(['customer_id'=>$this->customer_id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>'datetime DESC'
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'amount' => $this->amount,
            'date' => $this->date,
        ]);
        
        //$methods = \app\modules\checkout\models\PaymentMethod::getAccountMethods();
        //$ids = \yii\helpers\ArrayHelper::getColumn($methods, 'payment_method_id');
        
        //$query->andWhere(['payment_method_id'=>$ids]);

        $query->andFilterWhere(['like', 'concept', $this->concept])
            ->andFilterWhere(['like', 'number', $this->number]);

        return $dataProvider;
    }

    public function searchCashedByDate($params)
    {

        $this->load($params);

        if ($this->from) {
            $fromDate = new \DateTime($this->from);
        } else {
            $fromDate = new \DateTime('first day of this month');
        }
        if ($this->to) {
            $toDate = new \DateTime($this->to);
        } else {
            $toDate = new \DateTime('last day of this month');
        }

        $query = new Query();
        $query
            ->select(['c.name', 'pm.name as payment_method', 'sum(p.amount) as total'])
            ->from('company c')
            ->leftJoin('payment p', 'c.company_id = p.company_id')
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id')
            ->leftJoin('payment_method pm', 'pi.payment_method_id = pm.payment_method_id')
            ->andWhere(['>=', 'p.date', $fromDate->format('Y-m-d')])
            ->andWhere(['<=', 'p.date', $toDate->format('Y-m-d')])
            ->groupBy(['c.name', 'pm.name'])
        ;

        return $query->all();
    }
}