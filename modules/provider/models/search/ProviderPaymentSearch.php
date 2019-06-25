<?php

namespace app\modules\provider\models\search;

use app\components\db\BigDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\provider\models\ProviderPayment;
use yii\db\Expression;
use yii\db\Query;

/**
 * ProviderPaymentSearch represents the model behind the search form about `app\modules\provider\models\ProviderPayment`.
 */
class ProviderPaymentSearch extends ProviderPayment
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
            [['provider_payment_id', 'provider_id'], 'integer'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())->from('provider_payment')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query
            ->leftJoin('provider_payment_item ppi', 'provider_payment.provider_payment_id = ppi.provider_payment_id')
            ->leftJoin('payment_method pm', 'ppi.payment_method_id = pm.payment_method_id')
            ->leftJoin('provider_bill_has_provider_payment pbhpp', 'pbhpp.provider_payment_id = provider_payment.provider_payment_id')
            ->leftJoin('provider_bill pb', 'pb.provider_bill_id= pbhpp.provider_bill_id')
            ->leftJoin('company comp', 'comp.company_id= provider_payment.company_id')
            ->leftJoin('provider pro', 'provider_payment.provider_id = pro.provider_id')
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
            'provider_payment.provider_id' => $this->provider_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['>=', 'provider_payment.amount', $this->amount]);

        $query->select([
            'provider_payment.provider_payment_id', 'pro.provider_id', 'pro.name as provider', 'provider_payment.date',
            'provider_payment.status',
            new Expression('GROUP_CONCAT(pm.name) as payment_method'), 'provider_payment.amount'
        ]);
        $query->groupBy([
            'provider_payment.provider_payment_id',
            'provider_payment.status',
            'pro.provider_id',
            'pro.name',
            'provider_payment.date',
            'provider_payment.amount']
        );

        $query->orderBy(['provider_payment.date' => SORT_DESC]);

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function total()
    {
        $query = ProviderPayment::find();

        $query->where([
            'provider_id' => $this->provider_id,
        ]);

        $amount = $query->sum('amount');
        
        return $amount > 0 ? $amount : 0.0;
        
    }

    private function filterByDates($query){
        if (!empty($this->start_date)) {
            $query->andFilterWhere(['>=','provider_payment.date', Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd')]);
        }

        if (!empty($this->finish_date)) {
            $query->andFilterWhere(['<=','provider_payment.date', Yii::$app->formatter->asDate($this->finish_date, 'yyyy-MM-dd')]);
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


    public function searchAccount($provider_id, $params)
    {
        // Armo la consulta para las facturas de proveedor
        // Siempre filtro por proveedor y los multiplicadores
        $qBill = (new Query())
            ->select([new Expression('0 as orden'), 'bt.name AS type', 'p.provider_id',
                'p.name AS name', 'pb.provider_bill_id', new Expression('0 AS provider_payment_id'),
                'pb.date', 'pb.number', new Expression('(pb.total * bt.multiplier) AS total'),
                'pb.status', new Expression("'' AS payment_method"), new Expression("'' AS bill_numbers"),
                'c.name AS company_name'])
            ->from('provider_bill pb')
            ->leftJoin('bill_type bt', 'pb.bill_type_id = bt.bill_type_id')
            ->leftJoin('provider p', 'pb.provider_id = p.provider_id' )
            ->leftJoin('company c', 'pb.company_id = c.company_id' )
            ->where([
                'pb.provider_id'=>$provider_id,
                'bt.multiplier' => [-1,1]
            ]);
        ;

        // Armo las consulta de pagos, y le incluyo todas las facturas que paga
        $qPayment = (new Query())
            ->select([
                new Expression('1 as orden'), new Expression("'Payment' AS type"), 'p.provider_id',
                'p.name AS name', new Expression('0 AS provider_bill_id'), 'pp.provider_payment_id',
                'pp.date', new Expression('0 as number'), 'pp.amount as total',
                'pp.status', 'GROUP_CONCAT(DISTINCT pm.name) AS payment_method',
                new Expression("(select GROUP_CONCAT(DISTINCT coalesce(pb.number, concat('Nro.: ', pb.provider_bill_id))) AS bill_numbers from provider_bill_has_provider_payment pbhpp
      LEFT JOIN provider_bill pb ON pbhpp.provider_bill_id = pb.provider_bill_id where pbhpp.provider_payment_id = pp.provider_payment_id) AS bill_numbers"),
                'c.name as company_name'
            ])
            ->from('provider_payment pp')
            ->leftJoin('provider p', 'pp.provider_id = p.provider_id')
            ->leftJoin('provider_payment_item pmi', 'pp.provider_payment_id = pmi.provider_payment_id')
            ->leftJoin('payment_method pm', 'pmi.payment_method_id = pm.payment_method_id')
            ->leftJoin('company c', 'pp.company_id = c.company_id')
            ->where([
                'pp.provider_id'=>$provider_id,
            ])
            ->groupBy(['p.provider_id', 'p.name', 'pp.provider_payment_id', 'pp.amount', 'pp.date', 'pp.status', 'c.name'])
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
                new Expression('(@saldo := @saldo + if(b.provider_bill_id > 0, b.total, -b.total)) AS saldo2'),
                new Expression('(SELECT round(@saldo,2)) AS saldo')
            ])
            ->from(['b' => $qBill])
            ->orderBy(['date'=>SORT_ASC, 'orden'=> SORT_ASC])
        ;

        // Al fin.... termino de armar la consulta que se ejecuta. Hace join a provider para que no traiga
        // nada que no tenga proveedor
        $query = (new Query())
            ->select([ new Expression('SQL_CALC_FOUND_ROWS b.type'),
                'b.provider_id', 'b.name', 'b.provider_bill_id', 'b.provider_payment_id',
                'b.date', 'b.number', 'b.total', 'b.status', 'b.payment_method', 'b.bill_numbers',
                'b.company_name', 'b.saldo'
            ])
            ->from('provider as p')
            ->leftJoin(['b'=> $qSaldo], 'b.provider_id = p.provider_id')
            ->where('b.provider_id IS NOT NULL')->orderBy('b.date DESC');

        // Busco el saldo inicial
        $saldoInicial = (new Query())
            ->select([new Expression('sum(total * multiplier)')])
            ->from('provider_bill')
            ->leftJoin('bill_type', 'provider_bill.bill_type_id = bill_type.bill_type_id')
            ->where(['provider_id'=>$provider_id])
            ->andWhere(['<', 'date', $desde])
            ->scalar()
        ;

        $saldoInicial -= (new Query())
            ->select([new Expression('sum(amount)')])
            ->from('provider_payment')
            ->where(['provider_id'=>$provider_id])
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

    public function searchPendingBills($provider_id, $provider_payment_id)
    {
        $query = (new Query())
            ->select(['pb.provider_bill_id', 'pb.date','pb.type','pb.number','pb.net','pb.taxes',
                        'pb.total','pb.provider_id','bt.multiplier','bt.name AS bill_type','pp.amount',
                        new Expression('sum(coalesce(if(pbpy.provider_payment_id = '.$provider_payment_id.', pbpy.amount, 0), 0)) AS total_amount'),
                    new Expression('pb.total - sum(coalesce(pbpy.amount, 0)) AS balance')
            ] )
            ->from('provider_bill pb')
            ->leftJoin('provider_bill_has_provider_payment pbpy', 'pb.provider_bill_id = pbpy.provider_bill_id')
            ->leftJoin('provider_payment pp', 'pbpy.provider_payment_id = pp.provider_payment_id')
            ->leftJoin('bill_type bt', 'pb.bill_type_id = bt.bill_type_id')
            ->where(['pb.provider_id'=>$provider_id])
            ->groupBy(['pb.provider_bill_id', 'pb.date', 'pb.type', 'pb.number', 'pb.net', 'pb.taxes', 'pb.total',
                        'pb.provider_id', 'bt.multiplier', 'bt.name'])
            ->having('sum(coalesce(pbpy.amount, 0)) < pb.total or sum(coalesce(pbpy.amount, 0)) = 0 or sum(coalesce(if(pbpy.provider_payment_id = '.$provider_payment_id.', pbpy.amount, 0), 0)) > 0')
        ;

        return $query->orderBy(['provider_bill_id'=>SORT_ASC]);

    }
}