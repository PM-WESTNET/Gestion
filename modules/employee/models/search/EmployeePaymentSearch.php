<?php

namespace app\modules\employee\models\search;

use app\components\db\BigDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\EmployeePayment;
use yii\db\Expression;
use yii\db\Query;

/**
 * EmployeePaymentSearch represents the model behind the search form about `app\modules\employee\models\EmployeePayment`.
 */
class EmployeePaymentSearch extends EmployeePayment
{
    public $start_date;
    public $finish_date;
    public $bill_type_id;
    public $company_id;
    public $number;

    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_payment_id', 'employee_id'], 'integer'],
            [['date', 'description', 'start_date', 'finish_date', 'bill_type_id', 'company_id', 'number', 'type'], 'safe'],
            [['type'], 'string'],
            [['type'], 'default', 'value'=>'all'],
            [['amount'], 'number'],
        ];
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'start_date' => Yii::t('app', 'Start Date'),
            'finish_date' => Yii::t('app', 'Finish Date'),
            'bill_type_id' => Yii::t('app', 'Bill Type'),
            'company_id' => Yii::t('app', 'Company'),
            'type' => Yii::t('app', 'Type')
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
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())->from('employee_payment')
        ;

        $dataEmployee = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query
            ->leftJoin('employee_payment_item ppi', 'employee_payment.employee_payment_id = ppi.employee_payment_id')
            ->leftJoin('payment_method pm', 'ppi.payment_method_id = pm.payment_method_id')
            ->leftJoin('employee_bill_has_employee_payment pbhpp', 'pbhpp.employee_payment_id = employee_payment.employee_payment_id')
            ->leftJoin('employee_bill pb', 'pb.employee_bill_id= pbhpp.employee_bill_id')
            ->leftJoin('company comp', 'comp.company_id= employee_payment.company_id')
            ->leftJoin('employee pro', 'employee_payment.employee_id = pro.employee_id')
        ;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->filterByDates($query);
        $this->filterByCompany($query);
        $this->filterByBillType($query);

        $query->andFilterWhere([
            'employee_payment.employee_id' => $this->employee_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['>=', 'employee_payment.amount', $this->amount]);

        $query->select([
            'employee_payment.employee_payment_id', 'pro.employee_id', 'pro.name as employee', 'employee_payment.date',
            'employee_payment.status',
            new Expression('GROUP_CONCAT(pm.name) as payment_method'), 'employee_payment.amount'
        ]);
        $query->groupBy([
            'employee_payment.employee_payment_id',
            'employee_payment.status',
            'pro.employee_id',
            'pro.name',
            'employee_payment.date',
            'employee_payment.amount']
        );

        $query->orderBy(['employee_payment.date' => SORT_DESC]);

        return $dataProvider;
    }
    
    /**
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataEmployee
     */
    public function total()
    {
        $query = EmployeePayment::find();

        $query->where([
            'employee_id' => $this->employee_id,
        ]);

        $amount = $query->sum('amount');
        
        return $amount > 0 ? $amount : 0.0;
        
    }

    private function filterByDates($query){
        if (!empty($this->start_date)) {
            $query->andFilterWhere(['>=','employee_payment.date', Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd')]);
        }

        if (!empty($this->finish_date)) {
            $query->andFilterWhere(['<=','employee_payment.date', Yii::$app->formatter->asDate($this->finish_date, 'yyyy-MM-dd')]);
        }
    }

    private function filterByBillType($query){
        if (!empty($this->bill_type_id)) {
            $query->andFilterWhere(['=', 'pb.bill_type_id', $this->bill_type_id]);
        }
    }

    private function filterByCompany($query)
    {
        if (!empty($this->company_id)) {
            $query->andFilterWhere(['=', 'comp.company_id', $this->company_id]);
        }
    }


    public function searchAccount($employee_id, $params)
    {
        // Armo la consulta para las facturas de proveedor
        // Siempre filtro por proveedor y los multiplicadores
        $qBill = (new Query())
            ->select([new Expression('0 as orden'), 'bt.name AS type', 'p.employee_id',
                'p.name AS name', 'pb.employee_bill_id', new Expression('0 AS employee_payment_id'),
                'pb.date', 'pb.number', new Expression('(pb.total * bt.multiplier) AS total'),
                'pb.status', new Expression("'' AS payment_method"), new Expression("'' AS bill_numbers"),
                'c.name AS company_name'])
            ->from('employee_bill pb')
            ->leftJoin('bill_type bt', 'pb.bill_type_id = bt.bill_type_id')
            ->leftJoin('employee p', 'pb.employee_id = p.employee_id' )
            ->leftJoin('company c', 'pb.company_id = c.company_id' )
            ->where([
                'pb.employee_id'=>$employee_id,
                'bt.multiplier' => [-1,1]
            ]);
        ;

        // Armo las consulta de pagos, y le incluyo todas las facturas que paga
        $qPayment = (new Query())
            ->select([
                new Expression('1 as orden'), new Expression("'Payment' AS type"), 'p.employee_id',
                'p.name AS name', new Expression('0 AS employee_bill_id'), 'pp.employee_payment_id',
                'pp.date', new Expression('0 as number'), 'pp.amount as total',
                'pp.status', 'GROUP_CONCAT(DISTINCT pm.name) AS payment_method',
                new Expression("(select GROUP_CONCAT(DISTINCT coalesce(pb.number, concat('Nro.: ', pb.employee_bill_id))) AS bill_numbers from employee_bill_has_employee_payment pbhpp
      LEFT JOIN employee_bill pb ON pbhpp.employee_bill_id = pb.employee_bill_id where pbhpp.employee_payment_id = pp.employee_payment_id) AS bill_numbers"),
                'c.name as company_name'
            ])
            ->from('employee_payment pp')
            ->leftJoin('employee p', 'pp.employee_id = p.employee_id')
            ->leftJoin('employee_payment_item pmi', 'pp.employee_payment_id = pmi.employee_payment_id')
            ->leftJoin('payment_method pm', 'pmi.payment_method_id = pm.payment_method_id')
            ->leftJoin('company c', 'pp.company_id = c.company_id')
            ->where([
                'pp.employee_id'=>$employee_id,
            ])
            ->groupBy(['p.employee_id', 'p.name', 'pp.employee_payment_id', 'pp.amount', 'pp.date', 'pp.status', 'c.name'])
        ;

        $this->load($params);
        // Filtro por fecha
        $desde = (new \DateTime('now -6 month'))->format('Y-m').'-01';
        if(!empty($this->start_date)) {
            $desde = Yii::$app->formatter->asDate($this->start_date, 'Y-M-dd');
        }
        $hasta =  (new \DateTime(date('Y-m').'-01'))->modify('+1 month -1 day')->format('Y-m-d') ;
        if(!empty($this->finish_date)) {
            $hasta = Yii::$app->formatter->asDate($this->finish_date, 'Y-M-dd');
        }
        $this->start_date = Yii::$app->formatter->asDate($desde, 'dd-MM-Y');
        $this->finish_date = Yii::$app->formatter->asDate($hasta, 'dd-MM-Y');

        $qBill->andFilterWhere(['between', 'pb.date', $desde, $hasta]);
        $qPayment->andFilterWhere(['between', 'pp.date', $desde, $hasta]);

        // Uno las dos consultas para que me traiga todo mesclado
        if($this->type == 'all' || $this->type == '') {
            $qBill->union($qPayment);
        } else if($this->type == 'payment') {
            $qBill = $qPayment;
        }

        // Saco los saldos, redondeo a 2 decimales para que no traiga muchos 00000000000
        $qSaldo = (new Query())
            ->select(['b.*',
                new Expression('(@saldo := @saldo + if(b.employee_bill_id > 0, b.total, -b.total)) AS saldo2'),
                new Expression('(SELECT round(@saldo,2)) AS saldo')
            ])
            ->from(['b' => $qBill])
            ->orderBy(['date'=>SORT_ASC, 'orden'=> SORT_ASC])
        ;

        // Al fin.... termino de armar la consulta que se ejecuta. Hace join a employee para que no traiga
        // nada que no tenga proveedor
        $query = (new Query())
            ->select([ new Expression('SQL_CALC_FOUND_ROWS b.type'),
                'b.employee_id', 'b.name', 'b.employee_bill_id', 'b.employee_payment_id',
                'b.date', 'b.number', 'b.total', 'b.status', 'b.payment_method', 'b.bill_numbers',
                'b.company_name', 'b.saldo'
            ])
            ->from('employee as p')
            ->leftJoin(['b'=> $qSaldo], 'b.employee_id = p.employee_id')
            ->where('b.employee_id IS NOT NULL')->orderBy('b.date DESC');

        // Busco el saldo inicial
        $saldoInicial = (new Query())
            ->select([new Expression('sum(total * multiplier)')])
            ->from('employee_bill')
            ->leftJoin('bill_type', 'employee_bill.bill_type_id = bill_type.bill_type_id')
            ->where(['employee_id'=>$employee_id])
            ->andWhere(['<', 'date', $desde])
            ->scalar()
        ;

        $saldoInicial -= (new Query())
            ->select([new Expression('sum(amount)')])
            ->from('employee_payment')
            ->where(['employee_id'=>$employee_id])
            ->andWhere(['<', 'date', $desde ])
            ->scalar()
        ;

        // Por las dudas limpio la variable de Saldo
        Yii::$app->db->createCommand('set @saldo:='.$saldoInicial.';')->execute();
        $dataProvider = new BigDataProvider([
            'query' => $query,
           'pagination' => [
               'pageSize' => 20,
               'page' => (isset($params['page']) ? $params['page'] -1 : 0 )
            ],
       ]);

        return $dataProvider;
    }

    public function searchPendingBills($employee_id, $employee_payment_id)
    {
        $query = (new Query())
            ->select(['pb.employee_bill_id', 'pb.date','pb.type','pb.number','pb.net','pb.taxes',
                        'pb.total','pb.employee_id','bt.multiplier','bt.name AS bill_type','pp.amount',
                        new Expression('sum(coalesce(if(pbpy.employee_payment_id = '.$employee_payment_id.', pbpy.amount, 0), 0)) AS total_amount'),
                    new Expression('pb.total - sum(coalesce(pbpy.amount, 0)) AS balance')
            ] )
            ->from('employee_bill pb')
            ->leftJoin('employee_bill_has_employee_payment pbpy', 'pb.employee_bill_id = pbpy.employee_bill_id')
            ->leftJoin('employee_payment pp', 'pbpy.employee_payment_id = pp.employee_payment_id')
            ->leftJoin('bill_type bt', 'pb.bill_type_id = bt.bill_type_id')
            ->where(['pb.employee_id'=>$employee_id])
            ->groupBy(['pb.employee_bill_id', 'pb.date', 'pb.type', 'pb.number', 'pb.net', 'pb.taxes', 'pb.total',
                        'pb.employee_id', 'bt.multiplier', 'bt.name'])
            ->having('sum(coalesce(pbpy.amount, 0)) < pb.total or sum(coalesce(pbpy.amount, 0)) = 0 or sum(coalesce(if(pbpy.employee_payment_id = '.$employee_payment_id.', pbpy.amount, 0), 0)) > 0')
        ;

        return $query->orderBy(['employee_bill_id'=>SORT_ASC]);

    }
}