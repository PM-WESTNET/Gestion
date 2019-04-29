<?php

namespace app\modules\paycheck\models\search;

use app\modules\paycheck\models\Paycheck;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 4/08/15
 * Time: 16:10
 */
class PaycheckSearch extends Paycheck
{

    // Fechas
    public $toDueDate;
    public $fromDueDate;

    // Fechas
    public $toDate;
    public $fromDate;

    public $owned;

    public $name;

    public $statuses = [
        'created',
        'commited',
        'received',
        'canceled',
        'cashed',
        'rejected',
        'returned',
        'deposited',
    ];

    public function rules()
    {
        return [
            [['name', 'number'], 'string'],
            [['toDate', 'fromDate', 'toDueDate', 'fromDueDate'], 'safe'],
            [['toDate', 'fromDate', 'toDueDate', 'fromDueDate'], 'default', 'value'=>null],
            [['owned'], 'default', 'value'=>[0,1]],
            [['crossed'], 'default', 'value'=>2],
            [['to_order'], 'default', 'value'=>2],
            [['status'], 'in', 'range' => $this->statuses],
            ['statuses', 'each', 'rule' => ['in', 'range' => $this->statuses]],
            [['statuses'], 'default', 'value'=>$this->statuses],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'statuses' => Yii::t('app', 'Statuses'),
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
            'fromDueDate' => Yii::t('paycheck', 'Due From Date'),
            'toDueDate' => Yii::t('paycheck', 'Due To Date'),
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

    }


    /**
     * Busqueda regular
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->getQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['due_date'=>SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['paycheck.is_own'=>$this->owned]);

        //Estado/s de factura
        $this->filterStatus($query);

        //Fechas
        $this->filterDates($query);

        // Filtro por nombres
        $this->filterNames($query);

        // Filtro si es cruzado
        if ($this->crossed<2) {
            $query->andFilterWhere(['paycheck.crossed'=>$this->crossed]);
        }

        // Filtro si es a la orden
        if ($this->to_order<2) {
            $query->andFilterWhere(['paycheck.to_order'=>$this->to_order]);
        }

        // Filtro por numero
        if($this->number) {
            $query->andFilterWhere(['paycheck.number'=>$this->number]);
        }

        $query->orderBy(['paycheck.date' => SORT_DESC]);

        return $dataProvider;
    }

    private function getQuery()
    {
        return self::find()
            ->leftJoin('checkbook c', 'paycheck.checkbook_id = c.checkbook_id')
            ->leftJoin('money_box_account mba', 'c.money_box_account_id = mba.money_box_account_id')
            ->leftJoin('money_box mbOwn', 'mba.money_box_id = mbOwn.money_box_id')
            ->leftJoin('money_box mbNoOwn', 'mbNoOwn.money_box_id = paycheck.money_box_id');
    }

    /**
     * Aplica filtro a estado. Si statuses esta definido, aplica una condicion
     * "in". Sino aplica un "=" con status
     * @param ActiveQuery $query
     */
    private function filterStatus($query){

        if(!empty($this->statuses)){

            $query->andWhere([
                'paycheck.status' => $this->statuses,
            ]);

        }else{

            $query->andWhere([
                'paycheck.status' => $this->status,
            ]);
        }
    }

    /**
     * Agrega queries para filtrar por fechas
     * @param type $query
     */
    private function filterDates($query)
    {
        if (!empty($this->fromDate)) {
            $date = Yii::$app->formatter->asDate($this->fromDate, 'yyyy-MM-dd');
            $query->andWhere(['>=', 'paycheck.date', $date]);
        }

        if (!empty($this->toDate)) {
            $date = Yii::$app->formatter->asDate($this->toDate, 'yyyy-MM-dd');
            $query->andWhere(['<=', 'paycheck.date', $date]);
        }

        if (!empty($this->fromDueDate)) {
            $date = Yii::$app->formatter->asDate($this->fromDueDate, 'yyyy-MM-dd');
            $query->andWhere(['>=', 'paycheck.due_date', $date]);
        }

        if (!empty($this->toDueDate)) {
            $date = Yii::$app->formatter->asDate($this->toDueDate, 'yyyy-MM-dd');
            $query->andWhere(['<=', 'paycheck.due_date', $date]);
        }
    }

    /**
     * Agrega queries para filtrar por nombres de business_name del cheque
     * razon social de quien pago o a quien se paga.
     *
     * @param type $query
     */
    private function filterNames($query)
    {
        if(!empty($this->name)){
            $query->leftJoin('payment_item', 'payment_item.paycheck_id = paycheck.paycheck_id');
            $query->leftJoin('payment', 'payment.payment_id = payment_item.payment_id');
            $query->leftJoin('customer', 'customer.customer_id = payment.customer_id');
            $query->leftJoin('provider_payment_item', 'provider_payment_item.paycheck_id = paycheck.paycheck_id');
            $query->leftJoin('provider_payment', 'provider_payment.provider_payment_id = provider_payment_item.provider_payment_id');
            $query->leftJoin('provider', 'provider.provider_id = provider_payment.provider_id');
            $query->leftJoin('paycheck_log', 'paycheck_log.paycheck_id = paycheck.paycheck_id ');
            $query->andFilterWhere([
                'or',
                ['like', 'paycheck.business_name', $this->name],
                ['like', 'customer.name', $this->name],
                ['like', 'provider.business_name', $this->name],
                ['like', 'paycheck_log.description', $this->name],
                ['like', 'paycheck.description', $this->name],
            ]);
        }

    }


    /*
     * Retorna todos los cheques en cartera
     *
     * @return static
     */
    public function searchEnCartera($params)
    {
        $query = $this->getQuery();

        //Estado/s de factura
        $this->statuses = [
            self::STATE_RECEIVED,
            self::STATE_CREATED
        ];
        $this->owned = [0];

        if (array_key_exists('for_payment', $params)) {
            // Si es para pago incluyo los propios
            if ($params['for_payment']) {
                $this->statuses[] = self::STATE_CREATED;
                $this->owned[] = 1;
            } else {
                // Como no es para pago, sino que estoy cobrando, solo muestro los recibidos
                // y que no esten en ningun pago hecho.
                $subQuery = (new Query())
                    ->select(['paycheck_id'])
                    ->from('payment_item')
                    ->where(['is not', 'paycheck_id', null]);
                $query->andWhere([
                    'not in', 'paycheck.paycheck_id', $subQuery
                ]);
            }
        }
        
        $this->load($params);

        $query->andWhere(['paycheck.is_own'=>$this->owned]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['due_date'=>SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->filterStatus($query);

        //Fechas
        $this->fromDate = null;
        $this->fromDate = null;
        $this->toDueDate = null;
        $this->fromDueDate = date('d-m-Y');
        $this->filterDates($query);
        if (array_key_exists('paycheck_id', $params)) {
            $query->orWhere(['paycheck.paycheck_id'=>$params['paycheck_id']]);
        }
        return $dataProvider;
    }

}
