<?php

namespace app\modules\sale\modules\contract\models\search;

use app\components\helpers\DbHelper;
use app\modules\sale\modules\contract\models\Contract;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * ContractSearch represents the model behind the search form about `app\modules\sale\models\Contract`.
 */
class ContractSearch extends Contract {

    public $period;
    public $company_id;
    public $bill_type_id;
    public $contracts;
    public $customer_id;
    //Atributos para filtro de instalaciones pendientes
    public $document_number;
    public $customer_number;
    public $name;
    public $last_name;
    public $date;
    public $zone_id;
    public $tentative_node;
    
    //Atributos para instalaciones realizadas
    public $min_bills_count;
    public $max_bills_count;
    public $min_tickets_count;
    public $max_tickets_count;
    public $min_debt;
    public $max_debt;
    public $from_date;
    public $to_date;
    
    

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['contract_id', 'customer_id', 'address_id', 'contracts', 'vendor_id', 'customer_number', 'zone_id'], 'integer'],
            [['status', 'document_number', 'customer_number', 'name', 'last_name', 'date', 'vendor_id', 'tentative_node', 'zone_id', 'min_bills_count', 'max_bills_count', 'min_debt', 'max_debt', 'min_tickets_count', 'max_tickets_count', 'from_date', 'to_date'], 'safe'],
            [['period', 'date'], 'date'],
            [['period', 'company_id', 'bill_type_id'], 'required', 'on' => 'for-invoice'],
            [['vendor_id'], 'required', 'on' => 'vendor-search']
        ];
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['period'] = Yii::t('app', 'Period');
        $labels['bill_type_id'] = Yii::t('app', 'Bill Type');
        $labels['document_number']= Yii::t('app', 'Document Number');
        $labels['customer_number']= Yii::t('app', 'Customer Number');
        $labels['name']= Yii::t('app', 'Name');
        $labels['last_name']= Yii::t('app', 'Lastname');
        $labels['tentative_node']= Yii::t('app', 'Tentative Node');
        $labels['zone_id']= Yii::t('app', 'Zone');
        
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['for-invoice'] = ['period', 'company_id', 'bill_type_id'];
        return $scenarios;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Contract::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        //filtro por vendedor
        $query->andFilterWhere([
            'vendor_id' => $this->vendor_id
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'contract_id' => $this->contract_id,
            'customer_id' => $this->customer_id,
            'to_date' => $this->to_date,
            'from_date' => $this->from_date,
            'address_id' => $this->address_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByVendor($params) {
        $query = Contract::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        //filtro por vendedor
        $query->andFilterWhere([
            'vendor_id' => $this->vendor_id
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'contract_id' => $this->contract_id,
            'customer_id' => $this->customer_id,
            'to_date' => $this->to_date,
            'from_date' => $this->from_date,
            'address_id' => $this->address_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }

    public static function getdataProviderContract($customer_id = null) {
        $dataProvider = new ActiveDataProvider([
            'query' => Contract::find()->where(['customer_id' => $customer_id]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    public function searchForInvoice($params, $forBill = false, $includePlan=true) {
        $configDB = DbHelper::getDbName(Yii::$app->dbconfig);

        $this->load($params);

        if ($this->period instanceof DateTime) {
            $period = $this->period;
        } else {
            $period = new DateTime($this->period);
        }

        $nextPeriod = clone $period;
        $nextPeriod->modify('first day of next month');
        $last_day_of_the_month = (new DateTime($period->format('Y-m-d')))->modify('last day of this month')->format('Y-m-d');
        $today = (new DateTime('now'))->format('Y-m-d');

        $subQuery = (new Query())
                ->select(['c1.customer_id', 'c1.customer_class_id'])
                ->from('customer_class_has_customer as c1')
                ->leftJoin('customer_class_has_customer c2', 'c1.customer_id = c2.customer_id AND c1.date_updated < c2.date_updated')
                ->where('c2.customer_id is null');

        $subQueryPti = (new Query())
                ->select(['product_to_invoice_id', 'contract_detail_id', 'date_format(period, \'%Y%m\') as period'])
                ->from('product_to_invoice')
                ->where(['status' => 'consumed']);

        $where = ['and',
            (new Expression('date_format(cd.from_date, \'%Y%m\')<=date_format(current_date(), \'%Y%m\')')),
            ['cc.is_invoiced' => true],
            ['con.status' => 'active'],
            ['c.status' => 'enabled'],
            ['c.company_id' => $this->company_id],
            ['tchbt.bill_type_id' => $this->bill_type_id],
            ['<=', 'con.from_date', $last_day_of_the_month],
            ['>', 'cc.percentage_bill', 0],
            // Me fijo de que no haya un product_to_invoice y que el contract_detail sea valido para el periodo indicado
            // Ademas, verifico no tener una facturacion de un plan este mes, para evitar que se vuelva a faturar en la siguiente situación:
            // Luego de la facturacion por lotes a un cliente, se le cambia el plan de su contrato a la fecha actual,
            //despues, se genera la facturacion por lotes  y a ese cliente no se le debe facturar nuevamente
            (new Expression("
                    (`product_to_invoice_id` IS NULL AND(
                        (
                            `cd`.`from_date` <= '".$today."' AND `cd`.`to_date` >= '".$today."'
                        ) OR(
                            `cd`.`from_date` <= '".$today."' AND `cd`.`to_date` IS NULL
                        )
                    )) AND NOT (
                        select exists(
                            select * from product_to_invoice pti
                                left join contract_detail cd on cd.contract_detail_id = pti.contract_detail_id
                                left join product p on p.product_id = cd.product_id
                                where pti.status = 'consumed'
                                and p.type = 'plan'
                                and pti.period =  '".$period->format('Y-m-d')."'
                                and cd.contract_id = con.contract_id
                            )
                    )
		    "))];

        if($includePlan) {
            $where[] = ['p.type' => 'plan'];
        }

        if ($forBill) {
            $select = ['c.customer_id', 'con.contract_id'];
            $where[] = ['c.customer_id' => $this->customer_id];
            $groupBy = ['c.customer_id', 'con.contract_id'];
        } else {
            $select = ['c.customer_id','c.code', new Expression('concat(c.lastname, \' \',  c.name) as customer'), new Expression('count(distinct con.contract_id) as contracts')];
            $groupBy = ['c.customer_id', 'c.code', new Expression('concat(c.lastname, \' \',  c.name)')];
        }
        $query = new Query();
        $query->select($select)
                ->from('customer c')
                ->leftJoin('contract as con', 'c.customer_id = con.customer_id')
                ->leftJoin('contract_detail cd', 'con.contract_id = cd.contract_id')
                ->leftJoin('product p', 'cd.product_id = p.product_id')
                ->leftJoin('tax_condition_has_bill_type tchbt', 'c.tax_condition_id = tchbt.tax_condition_id')
                ->leftJoin(['cchc' => $subQuery], 'c.customer_id = cchc.customer_id')
                ->leftJoin('customer_class cc', 'cc.customer_class_id = cchc.customer_class_id')
                ->leftJoin(['pti' => $subQueryPti], 'pti.contract_detail_id = cd.contract_detail_id AND ( pti.period = ' . $period->format('Ym') . " OR (" .
                    " pti.period = if( con.from_date >= date_add(date_format(now(), '%Y-%m-01'), INTERVAL (select value from `$configDB`.config where item_id = (select item_id from `$configDB`.item where attr = 'contract_days_for_invoice_next_month'))-1 DAY), " . $nextPeriod->format('Ym') . ", " . $period->format('Ym') . ") "
                        . ") ) ")
                ->andWhere($where)
                ->groupBy($groupBy);

        return $query;
    }

    public function findByCustomerForSelect($customer_id) {
        $query = new Query();
        return $query
                        ->select(['contract_id as id', 'description as name'])
                        ->from(['contract'])
                        ->where(['customer_id' => $customer_id])
                        ->all();
    }

    public function searchWithoutConnections($params) {

        $query = Contract::find();
        $query->innerJoin('customer c', 'contract.customer_id = c.customer_id');
        $query->innerJoin('address a', 'contract.address_id = a.address_id');

        $query->where(['=', '(SELECT COUNT(*) FROM connection WHERE (connection.contract_id = contract.contract_id))', 0]);
        $query->andWhere(['=', 'contract.status', Contract::STATUS_DRAFT]);

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => new \yii\data\Sort(
                    [
                        'attributes' =>
                            [
                                'date' ,
                                'c.code',
                                'c.name',
                                'tentative_node' ,
                                'vendor_id'
                            ]
                    ]
                )
            ]
        );

        $this->load($params);


        /**if (!$this->validate()) {
            return $dataProvider;
        }**/

        

        if (isset($params['ContractSearch'])) {
                        
            if ($this->customer_number !== '') {    
                $query->andFilterWhere(['c.code' => $this->customer_number]);
            }
            if ($this->document_number !== '') {
                $query->andFilterWhere(['c.document_number' => $this->document_number]);
            }

            if ($this->vendor_id !== '') {
                $query->andFilterWhere(['contract.vendor_id' => $this->vendor_id]);
            }

            if ($this->name !== '') {
                $query->andFilterWhere(['like', 'c.name', $this->name]);
            }

            if ($this->last_name !== '') {
                $query->andFilterWhere(['like', 'c.lastname', $this->last_name]);
            }

            if ($this->date !== '') {
                $query->andFilterWhere(['=', 'contract.date', Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')]);
            }
            
            if ($this->zone_id !== '') {
                $query->andFilterWhere(['a.zone_id'=> $this->zone_id]);
            }
            
            if ($this->tentative_node !== '') {
                if($this->tentative_node !== 'null'){
                    $query->andFilterWhere(['contract.tentative_node'=> $this->tentative_node]);
                }else{
                    $query->andWhere('contract.tentative_node IS NULL');
                }
            }

            
        }
        return $dataProvider;
    }
    
    public function getInstallations($params, $billsQuery) {        
        
        
       $subQueryBills = (new Query())
            ->select(['sum(b.total * bt.multiplier) as amount'])
            ->from('bill b')
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
            ->where("b.status = 'closed' and b.customer_id = contract.customer_id");

        $qMethodPayment = (new Query())->select(['payment_method_id'])
                ->from('payment_method')
                ->where("type='account'");
        
        $subQueryPayments = (new Query())
            ->select(['sum(pi.amount)'])
            ->from('payment p')
            ->leftJoin('payment_item pi', 'p.payment_id = pi.payment_id and pi.payment_method_id NOT IN('.$qMethodPayment->createCommand()->getSql().')')
            ->where("p.status <> 'cancelled' and p.customer_id = contract.customer_id");

        if (!empty($this->toDate)){
            $subQueryPayments->andWhere(['<=', 'p.date', Yii::$app->getFormatter()->asDate($this->toDate, 'yyyy-MM-dd') ]);
        }
        
        if (!empty($this->fromDate)){
            $subQueryPayments->andWhere(['>=', 'p.date', Yii::$app->getFormatter()->asDate($this->fromDate, 'yyyy-MM-dd') ]);
        }
       
        $dbticket = DbHelper::getDbName(\Yii::$app->dbticket);

        $query = new Query();
        $query->select(['contract.contract_id', 'contract.address_id', 'contract.from_date', 'contract.customer_id' , 'c.email', 'c.code', "CONCAT(c.lastname, ', ', c.name) AS name", "CONCAT(c.phone, ' ', c.phone2, ' ', c.phone3) AS phones", 'if(bills is null,0, bills) as bills', 'round(coalesce(('.$subQueryBills->createCommand()->getRawSql().'), 0) - coalesce(('.$subQueryPayments->createCommand()->getRawSql().'), 0)) as saldo', 'COUNT(t.ticket_id) as ticket_count' ]);
        $query->from('contract');
        $query->innerJoin('customer c', 'contract.customer_id = c.customer_id');
        $query->leftJoin('address a', 'contract.address_id = a.address_id');
        $query->leftJoin(['b' => $billsQuery], 'b.customer_id = contract.customer_id');
        $query->leftJoin(['t' => "(SELECT * FROM $dbticket.ticket)"], 't.customer_id = contract.customer_id');
        
        $query->andWhere(['=', 'contract.status', Contract::STATUS_ACTIVE]);
        $query->orderBy(['contract.from_date' => SORT_DESC]);
        $query->groupBy(['contract.contract_id']);
 

        $this->load($params);


        /**if (!$this->validate()) {
            return $dataProvider;
        }**/
        
                        
            if ($this->customer_number !== '') {    
                $query->andFilterWhere(['c.code' => $this->customer_number]);
            }
           
            if ($this->vendor_id !== '') {
                $query->andFilterWhere(['contract.vendor_id' => $this->vendor_id]);
            }

            if ($this->name !== '') {
                $query->andFilterWhere(['like', 'c.name', $this->name]);
            }

            if ($this->last_name !== '') {
                $query->andFilterWhere(['like', 'c.lastname', $this->last_name]);
            }

            if ($this->from_date !== '') {
                $query->andFilterWhere(['>=', 'contract.from_date', Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd')]);
            }
            
            if ($this->to_date !== '') {
                $query->andFilterWhere(['<=', 'contract.from_date', Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd')]);
            }
            
            if ($this->zone_id !== '') {
                $query->andFilterWhere(['a.zone_id'=> $this->zone_id]);
            }
            
            if ($this->min_tickets_count !== '' && !empty($this->min_tickets_count)) {
                $query->having(['>=', 'ticket_count', $this->min_tickets_count]);
            }
            
            if ($this->max_tickets_count !== '' && !empty($this->max_tickets_count)) {
                $query->having(['<=', 'ticket_count', $this->max_tickets_count]);
            }            
            
            
        $masterQuery= (new Query())
                ->select('*')
                ->from(['q' => $query]);
        
        if ($this->min_bills_count !== '') {
            $masterQuery->andFilterWhere(['>=', 'bills', $this->min_bills_count]);
        }
            
        if ($this->max_bills_count !== '') {
            $masterQuery->andFilterWhere(['<=', 'bills', $this->max_bills_count]);
        }

        if ($this->min_debt !== '') {
            $masterQuery->andFilterWhere(['>=', 'saldo', $this->min_debt]);
        }

        if ($this->max_debt !== '') {
            $masterQuery->andFilterWhere(['<=', 'saldo', $this->max_debt]);
        }
        
        $masterQuery->orderBy(['from_date' => SORT_ASC]);

        return $masterQuery;
    }

}
