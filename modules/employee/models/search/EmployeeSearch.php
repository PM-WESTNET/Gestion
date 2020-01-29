<?php

namespace app\modules\employee\models\search;

use app\modules\employee\models\EmployeeBill;
use app\modules\employee\models\EmployeePayment;
use Other\Space\Extender;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\Employee;
use yii\db\Expression;
use yii\db\Query;

/**
 * EmployeeSearch represents the model behind the search form about `app\modules\employee\models\Employee`.
 */
class EmployeeSearch extends Employee
{
    public $balance;
    public $fromDate;
    public $toDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id'], 'integer'],
            [['name', 'lastname', 'document_number',  'phone', 'fromDate', 'toDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fromDate' => Yii::t('app', 'From Date'),
            'toDate' => Yii::t('app', 'To Date'),
        ];
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
     * Funcion para buscar por name, bussiness_name o tax_identification
     * @param $name
     */
    public function searchBy($by)
    {
        /** @var Query $query */
        $query = Employee::find();
        $query->orWhere(['like', 'name', $by ])
            ->orWhere(['like', 'lastname', $by ])
            ->orWhere(['like', 'document_number', $by ])
            ->orderBy(['name'=>SORT_ASC]);

        return $query;

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
        $query = Employee::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'employee_id' => $this->employee_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['document_number' => $this->document_number])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

    public function searchDebts($params)
    {
        $this->load($params);
        $toDate = $this->toDate;
        if (!empty($toDate)){
            $toDate = Yii::$app->getFormatter()->asDate($toDate, 'yyyy-MM-dd');
        }


        $qBill = (new Query())
            ->select(['p.employee_id', 'p.name', new Expression('sum(pb.total*bt.multiplier) AS debt'), new Expression('0 as payment')])
            ->from('employee as p')
            ->leftJoin('employee_bill as pb', 'p.employee_id = pb.employee_id')
            ->leftJoin('bill_type as bt', 'pb.bill_type_id = bt.bill_type_id')
            ->groupBy(['p.employee_id', 'p.name']);
        ;

        $qPayment = (new Query())
            ->select(['p.employee_id', 'p.name', new Expression('0 AS debt'), new Expression('sum(pp.amount) as payment')])
            ->from('employee as p')
            ->leftJoin('employee_payment as pp', 'pp.employee_id = p.employee_id')
            ->groupBy(['p.employee_id', 'p.name']);
        ;

        if($toDate) {
            $qBill->andWhere(['<=', 'pb.date', $toDate]);
            $qPayment->andWhere(['<=', 'pp.date', $toDate]);
        }

        $qBill->union($qPayment, true);

        $query = (new Query())
            ->select(['employee_id', 'name', new Expression('sum(debt) as debt'), new Expression('sum(payment) as payment'),
                new Expression('sum(debt) - sum(payment) as balance')
            ])
            ->from(['a'=>$qBill])
            ->groupBy(['employee_id', 'name'])
            ->having(new Expression('round((sum(debt) - sum(payment))) <> 0'));



        if($this->employee_id) {
            $query->andWhere(['employee_id' => $this->employee_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['name' => SORT_ASC],
            'desc' => ['name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['balance'] = [
            'asc' => ['balance' => SORT_ASC],
            'desc' => ['balance' => SORT_DESC],
        ];
        return $dataProvider;
    }


    /**
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function accountTotalBills()
    {

        $query = EmployeeBill::find();
        $query->joinWith(['billType']);

        $query->where([
            'employee_id' => $this->employee_id,
        ]);

        $query->addSelect(['bill_type.multiplier']);

        return $query->sum('total * multiplier');
    }

    /**
     * Creates data employee instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function accountTotalPayed()
    {

        $query = EmployeePayment::find();

        $query->where([
            'employee_id' => $this->employee_id,
        ]);
        return $query->sum('amount');
    }

    /**
     * Busco todas las facturas y pagos agrupados por proveedor.
     * @param $params
     * @return Query
     */
    public function findBillsAndPayments($params)
    {
        $this->load($params);

        $toDate = $this->toDate;
        $fromDate = $this->fromDate;
        if (empty($toDate)){
            $toDate = (new \DateTime('last day of this month'))->format('d-m-Y');
        }
        if (empty($fromDate)){
            $fromDate = (new \DateTime('first day of this month'))->format('d-m-Y');
        }

        $toDate = Yii::$app->getFormatter()->asDate($toDate, 'yyyy-MM-dd');
        $fromDate = Yii::$app->getFormatter()->asDate($fromDate, 'yyyy-MM-dd');


        $subQuery = new Query();
        $subQuery
            ->select(['employee_id', 'sum(pb.total * pt.multiplier) AS billed'])
            ->from('employee_bill pb')
            ->leftJoin('bill_type pt','pb.bill_type_id = pt.bill_type_id')
            ->andWhere(['and', ['>=', 'pb.date', $fromDate], ['<=', 'pb.date', $toDate] ])
            ->groupBy(['employee_id'])
        ;


        $query = new Query();
        $query
            ->select(['p.employee_id', 'p.name', 'pb.billed as facturado', 'sum(pp.amount) as pagos'])
            ->from('employee p')
            ->leftJoin(['pb'=>$subQuery], 'p.employee_id = pb.employee_id')
            ->leftJoin('employee_payment pp', 'p.employee_id = pp.employee_id')
            ->groupBy(['p.employee_id', 'p.name'])
        ;


        $query
            ->andWhere(['and', ['>=', 'pp.date', $fromDate], ['<=', 'pp.date', $toDate] ])
        ;

        return $query;
    }

    /**
     * Retorno los totales de facturas y pagos.
     *
     * @param $params
     * @return array|bool
     */
    public function findBillsAndPaymentsTotals($params)
    {
        $queryPro = $this->findBillsAndPayments($params);

        $query = new Query();
        $query->select(['sum(p.facturado) as billed', 'sum(p.pagos) as payed'])
            ->from(['p' => $queryPro])
        ;

        return $query->one();
    }
}