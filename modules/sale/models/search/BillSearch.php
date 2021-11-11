<?php

namespace app\modules\sale\models\search;

use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Bill;
use yii\db\Expression;
use yii\db\Query;

/**
 * BillSearch represents the model behind the search form about `app\modules\sale\models\Bill`.
 */
class BillSearch extends Bill
{

    //Fechas
    public $toDate;
    public $fromDate;

    //Granularidad en history
    public $granularity = 'monthly';

    public $chartType = 'line';

    //Total correspondiente a un periodo en history
    public $periodTotal;

    //Montos
    public $fromAmount;
    public $toAmount;

    //Estados (in)
    public $statuses;

    //Bill types (in)
    public $bill_types;

    //Payment methods (in)
    public $payment_methods;
    public $payment_method_id;

    public $payment_method;

    //Classes (in)
    public $classes;

    //Expired, not expired, or all??
    public $expired;

    //Para searchWithDebt
    public $amountApplied;

    /**
     * Instancia un nuevo objeto de acuerdo al tipo. El objeto puede ser:
     *  Order.
     *  Bill.
     * @param array $row
     * @return \app\modules\sale\models\Plan|\app\modules\sale\models\Service|\self
     */
    public static function instantiate($row)
    {
        return new self;
    }

    public function rules()
    {
        $statuses = ['draft', 'completed', 'closed'];

        return [
            [['bill_id', 'number', 'customer_id', 'currency_id', 'bill_type_id', 'company_id', 'user_id'], 'integer'],
            [['date', 'time', 'toDate', 'fromDate', 'ein', 'class', 'classes', 'payment_methods'], 'safe'],
            [['toDate', 'fromDate'], 'default', 'value' => null],
            [['amount'], 'number'],
            [['granularity'], 'in', 'range' => ['daily', 'monthly', 'yearly']],
            [['chartType'], 'in', 'range' => [false, 'line', 'bar', 'radar']],
            [['fromAmount', 'toAmount'], 'double'],
            [['status'], 'in', 'range' => $statuses],
            [['payed', 'expired'], 'boolean'],
            ['bill_types', 'each', 'rule' => ['integer']],
            ['payment_methods', 'each', 'rule' => ['integer']],
            ['statuses', 'each', 'rule' => ['in', 'range' => $statuses]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fromAmount' => Yii::t('app', 'From Amount'),
            'toAmount' => Yii::t('app', 'To Amount'),
            'statuses' => Yii::t('app', 'Statuses'),
            'bill_types' => Yii::t('app', 'Bill Types'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'granularity' => Yii::t('app', 'Granularity'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'customer_id' => Yii::t('app', 'Customer'),

        ]);
    }


    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function init()
    {

        parent::init();
        $this->bill_type_id = null;
        $this->payed = null;
        $this->payment_method_id = null;
        $this->expired = 0;
        $this->footprint = null;
        $this->active = true;

    }

    /**
     * Busqueda regular
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['timestamp' => SORT_DESC],
                'attributes' => [
                    'timestamp' => [
                        'asc' => ['bill.timestamp' => SORT_ASC],
                        'desc' => ['bill.timestamp' => SORT_DESC],
                        'default' => SORT_DESC
                    ],
                ]
            ]
        ]);

        //Bills sin details no se muestran:
        //$query->joinWith('billDetails', false, 'INNER JOIN');

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=2');
            return $dataProvider;
        }

        $where = [
            'number' => $this->number,
            'amount' => $this->amount,
            'bill.customer_id' => $this->customer_id,
            'currency_id' => $this->currency_id,
            'payed' => $this->payed,

            'active' => $this->active,
            'footprint' => $this->footprint,
            'user_id' => $this->user_id
        ];

        // Verifico si el company es del parent.
        if ($this->company_id) {
            $company = Company::findOne(['company_id' => $this->company_id]);
            if ($company->parent_id) {
                $where['bill.company_id'] = $this->company_id;
            } else {
                $query->leftJoin('customer c', 'bill.customer_id = c.customer_id');
                $where['c.parent_company_id'] = $this->company_id;
            }
        }

        $query->andFilterWhere($where);

        //Montos
        $query->andFilterWhere(['>=', 'bill.total', $this->fromAmount]);
        $query->andFilterWhere(['<=', 'bill.total', $this->toAmount]);

        //Estado/s de factura
        $this->filterStatus($query);

        //Tipo/s de factura
        $this->filterType($query);

        //Fechas
        $this->filterDates($query);

        $query->joinWith(['billType']);


        //Payment Method
        $this->filterPaymentMethod($query);

        //Classes
        $this->filterClasses($query);

        //Expiration
        $this->periodTotal = $query->sum('total * multiplier');

        $countQuery = clone $query;
        $dataProvider->setTotalCount($countQuery->count());

        return $dataProvider;
    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterStatus($query)
    {

        if (!empty($this->statuses)) {

            $query->andFilterWhere([
                'bill.status' => $this->statuses,
            ]);

        } else {

            $query->andFilterWhere([
                'bill.status' => $this->status,
            ]);

        }

    }

    /**
     * Filtra por tipos. Si el array bill_types tiene valores, agrega una
     * condicion "in". Caso contrario, agrega un "=" con bill_type_id si este
     * esta definido.
     * @param ActiveQuery $query
     */
    private function filterType($query)
    {

        if (!empty($this->bill_types)) {

            $query->andFilterWhere([
                'bill.bill_type_id' => $this->bill_types,
            ]);

        } else {

            $query->andFilterWhere([
                'bill.bill_type_id' => $this->bill_type_id,
            ]);

        }

    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterDates($query)
    {
        if (empty($this->fromDate) && empty($this->toDate)) {
            $this->fromDate = (new \DateTime('now -1 month'))->format('Y-m-d');
            $query->andFilterWhere(['>=', "bill.timestamp", strtotime($this->fromDate)]);
        } else if (!empty($this->fromDate) && empty($this->toDate)) {
            $this->fromDate = Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd');
            $query->andFilterWhere(['>=', "bill.timestamp", strtotime($this->fromDate)]);
        } else if (empty($this->fromDate) && !empty($this->toDate)) {
            $this->toDate = Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd');
            $query->andFilterWhere(['<=', "bill.timestamp", strtotime($this->toDate)]);
        } else {
            $this->fromDate = Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd');
            $this->toDate = Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd');

            //Ingnoramos si el valor esta vacio
            //var_dump(strtotime($this->toDate));die();
            $query->andFilterWhere(['and',
                ['>=', "bill.timestamp", strtotime($this->fromDate)],
                ['<=', "bill.timestamp", strtotime($this->toDate)+84600]
            ]);
        }
    }

    /**
     * Filtra por metodos de pago. Si el array payment_methods tiene valores, agrega una
     * condicion "in". Caso contrario, agrega un "="
     * @param ActiveQuery $query
     */
    private function filterPaymentMethod($query)
    {

        if (!empty($this->payment_methods)) {

            // Para poder traer las formas de pago aplicadas al comprobante, se tiene que hacer join
            // a cada pago aplicado, como la forma de pago la tiene el item, hay que llegar hasta esa tabla.
            $query->leftJoin('bill_has_payment bhp', 'bhp.bill_id = bill.bill_id');
            $query->leftJoin('payment', 'payment.payment_id = bhp.payment_id');
            $query->leftJoin('payment_item', 'payment.payment_id = payment_item.payment_id');
            //$query->leftJoin('payment_method', 'payment_method.payment_method_id = payment_item.payment_method_id');

            $query->andFilterWhere(['payment_item.payment_method_id' => $this->payment_methods ]);
        }

    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterClasses($query)
    {

        if (!empty($this->classes)) {

            $query->andFilterWhere([
                'bill.class' => $this->classes,
            ]);

        } else {

            $query->andFilterWhere([
                'like', 'bill.class', "%$this->classes", false
            ]);

        }

    }

    /**
     * Busquedas con group by
     * @param [] $params
     * @return \yii\data\ActiveDataProvider
     */
    public function searchHistory($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $query->select(['date', 'round(SUM(total * bill_type.multiplier)) AS total', 'bill.class']);
        $query->joinWith(['billType']);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('1=2');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'bill.customer_id' => $this->customer_id,
            'currency_id' => $this->currency_id,
            'payed' => $this->payed,
            'company_id' => $this->company_id,
            'active' => true
        ]);

        switch ($this->granularity) {
            case 'daily':
                $query->groupBy = ['date'];
                break;
            case 'monthly':
                //En el caso del mes, nos interesa agrupar por mes de cada anio
                $query->groupBy = ['MONTH(date)', 'YEAR(date)'];
                break;
            case 'yearly':
                $query->groupBy = ['YEAR(date)'];
                break;
        }

        //Estado/s de factura
        $this->filterStatus($query);

        //Tipo/s de factura
        $this->filterType($query);

        //Fechas
        $this->filterDates($query);

        //Classes
        $this->filterClasses($query);

        //Expiration
        $this->filterExpired($query);

        $query->orderBy(['date' => SORT_ASC]);

        $models = [];
        $this->periodTotal = 0.0;

        foreach ($query->each() as $bill) {

            if (empty($bill->total)) {
                $bill->total = 0.0;
            }

            //Calculamos el total para los modelos del periodo
            $this->periodTotal += $bill->total;

            //Colocamos la fecha formateada de acuerdo a la granularidad del periodo
            $bill->granularity = $this->granularity;
            $bill->date = $bill->getGranularityDate();

            $models[] = $bill;
        }

        //Seteamos los modelos al provider
        $dataProvider->setModels($models);
        return $dataProvider;
    }

    /**
     * Devuelve el string que se debe aplicar en groupBy para generar el grafico,
     * basado en la granularidad
     * @return string
     */
    public function getGranularityDate()
    {

        $formatter = Yii::$app->formatter;

        switch ($this->granularity) {
            case 'daily':
                return $formatter->asDate($this->date, 'dd-MM-yyyy');
                break;
            case 'monthly':
                return ucfirst($formatter->asDate($this->date, 'MMMM yyyy'));
                break;
            case 'yearly':
                return $formatter->asDate($this->date, 'yyyy');
                break;
        }

    }

    /**
     * Expired or not expired
     * @param type $query
     */
    public function filterExpired($query)
    {

        if ($this->expired == false) {
            $query->andWhere('expiration_timestamp > ' . time() . ' OR expiration_timestamp IS NULL');
        } elseif ($this->expired == true) {
            $query->andWhere('expiration_timestamp <= ' . time() . ' AND expiration_timestamp IS NOT NULL');
        }

    }

    /**
     * Devuelve los tipos de comprobante que estan siendo buscados
     * @return type
     */
    public function getBillTypes()
    {

        $types = \app\modules\sale\models\BillType::find()->where(['bill_type_id' => $this->bill_types])->all();
        return $types;

    }

    public function searchWithDebt($params)
    {
        /** @var Query $query */
        $query = $this->find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['timestamp' => SORT_ASC]
            ]
        ]);

        $query->addSelect(['bill.*', 'sum(coalesce(bill_has_payment.amount, 0)) as amountApplied']);
        $query->joinWith(['billType']);
        $query->join('LEFT JOIN', 'bill_has_payment', 'bill.bill_id = bill_has_payment.bill_id');


        $this->load($params);

        if (!$this->validate()) {
            $query->where('1=2');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'bill.customer_id' => $this->customer_id,
            'bill_type.multiplier' => [-1, 1]
        ]);


        //Estado/s de factura
        $this->filterStatus($query);

        //Tipo/s de factura
        $this->filterType($query);

        //Fix pagination
        $query->groupBy('bill.bill_id');
        $query->having("sum(coalesce(bill_has_payment.amount, 0))< bill.amount");

        /*$query->andFilterWhere([
            'bill.bill_type_id' => $this->bill_types,
        ]);*/

        return $dataProvider;
    }

    public function seachWithOutElectronic($params)
    {
        $this->load($params);
        /** @var Query $query */
        $query = $this->find()
            ->joinWith(['billType'])
            ->where('bill.ein is null and bill.status in (\'draft\', \'completed\') AND bill_type.invoice_class_id IS NOT NULL and total <>0');

        $query->andFilterWhere(['bill.company_id' => $this->company_id])
            ->andFilterWhere(['bill.bill_type_id' => $this->bill_type_id]);

        return $query;
    }

    public function searchPendingToClose($params)
    {
        $this->load($params);

        echo 'company '.$this->company_id."\n";
        echo 'bill_type '.$this->bill_type_id."\n";
        $query = $this->find()
            ->where(['in','status',['draft', 'completed']])
            ->andFilterWhere(['automatically_generated' => 1])
            ->andFilterWhere(['company_id' => $this->company_id])
            ->andFilterWhere(['bill_type_id' => $this->bill_type_id]);

        if($this->fromDate) {
            $query->andFilterWhere(['>=','date',$this->fromDate]);
        }

        if($this->toDate) {
            $query->andFilterWhere(['<=','date',$this->toDate]);
        }

        return $query;
    }

    public function searchBilledByDate($params)
    {

        $this->load($params);

        if ($this->fromDate) {
            $fromDate = new \DateTime($this->fromDate);
        } else {
            $fromDate = new \DateTime('first day of this month');
        }
        if ($this->toDate) {
            $toDate = new \DateTime($this->toDate);
        } else {
            $toDate = new \DateTime('last day of this month');
        }

        $qBillType = new Query();
        $billTypes = $qBillType->select(['group_concat(bill_type_id) as bill_type_id', 'multiplier'])
            ->from('bill_type')
            ->groupBy(['multiplier'])
            ->all();

        $outSelect = ['c.name'];
        $inSelect = ['c.name'];
        foreach ($billTypes as $billType) {
            $name = ($billType['multiplier'] > 0 ? 'FC' : 'NC');
            $outSelect[] = 'sum(' . $name . ') as ' . $name;
            $inSelect[] = new Expression('CASE WHEN bt.bill_type_id IN (' . $billType['bill_type_id'] . ') THEN sum(b.amount * bt.multiplier) ELSE 0 END AS ' . $name);
        }
        $outSelect[] = 'sum(taxes) as taxes';
        $inSelect[] = 'sum(b.taxes) as taxes';

        $query = new Query();
        $query
            ->select($inSelect)
            ->from('company c')
            ->leftJoin('bill b', 'c.company_id = b.company_id')
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
            ->andWhere(['>=', 'b.date', $fromDate->format('Y-m-d')])
            ->andWhere(['<=', 'b.date', $toDate->format('Y-m-d')])
            ->groupBy(['c.name', 'bt.multiplier']);

        $mainQuery = new Query();
        $mainQuery
            ->select($outSelect)
            ->from(['c' => $query])
            ->groupBy(['c.name']);

        return $mainQuery->all();
    }

    /**
     * @param $params
     * @return mixed
     * Devuleve los ultimos comprobantes de cada cliente activo.
     */
    public function searchLastBills($params)
    {
        $this->load($params);

        $subquery = (new Query());
        $subquery->select('MAX(bill_id) as bill_id, customer_id')
            ->from('bill')
            ->groupBy('customer_id');

        $query  = self::find()
            ->select('b1.bill_id, b1.date, b1.bill_type_id, b1.number, b1.customer_id, b1.company_id')
            ->from('bill as b1')
            ->innerJoin(['last' => $subquery], 'last.bill_id = b1.bill_id')
            ->leftJoin('customer as c', 'c.customer_id = b1.customer_id')
            ->andWhere(['b1.status' => 'closed'])
            ->andWhere('b1.number IS NOT NULL')
            ->andWhere('b1.bill_type_id IS NOT NULL')
            ->andWhere('c.customer_id IS NOT NULL');


        if($this->fromDate){
            $query->andWhere(['>=','b1.date', (new \DateTime($this->fromDate))->format('Y-m-d')]);
        }

        if($this->toDate){
            $query->andWhere(['<=', 'b1.date', (new \DateTime($this->toDate))->format('Y-m-d')]);
        }

        if($this->company_id){
            $query->andWhere(['b1.company_id' => $this->company_id]);
        }


        return $query;
    }

    static public function searchLastBillByCustomerId($customerId){
        $query  = self::find()
            ->select('b1.bill_id, b1.date, b1.bill_type_id, b1.number, b1.customer_id, b1.company_id')
            ->from('bill as b1')
            ->leftJoin('customer as c', 'c.customer_id = b1.customer_id')
            ->andWhere(['b1.status' => 'closed'])
            ->andWhere('b1.number IS NOT NULL')
            ->andWhere('b1.bill_type_id IS NOT NULL')
            ->andWhere('c.customer_id IS NOT NULL');

        /*
        select *
        from bill b
        join customer c
        on b.customer_id = c.customer_id
        where c.customer_id = 61190
        and b.status != 'draft'
        order by b.bill_id desc
        limit 15
        */
        return $query;
    }
}