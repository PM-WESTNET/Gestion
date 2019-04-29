<?php

namespace app\modules\provider\models\search;

use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderPayment;
use Other\Space\Extender;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\provider\models\Provider;
use yii\db\Expression;
use yii\db\Query;

/**
 * ProviderSearch represents the model behind the search form about `app\modules\provider\models\Provider`.
 */
class ProviderSearch extends Provider
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
            [['provider_id'], 'integer'],
            [['name', 'business_name', 'tax_identification', 'address', 'bill_type', 'phone', 'phone2', 'description', 'fromDate', 'toDate'], 'safe'],
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
        $query = Provider::find();
        $query->orWhere(['like', 'name', $by ])
            ->orWhere(['like', 'business_name', $by ])
            ->orWhere(['like', 'tax_identification', $by ])
            ->orderBy(['name'=>SORT_ASC]);

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
        $query = Provider::find();

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
            'provider_id' => $this->provider_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'business_name', $this->business_name])
            ->andFilterWhere(['tax_identification' => $this->tax_identification])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['bill_type' => $this->bill_type])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'phone2', $this->phone2])
            ->andFilterWhere(['like', 'description', $this->description]);

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
            ->select(['p.provider_id', 'p.name', new Expression('sum(pb.total*bt.multiplier) AS debt'), new Expression('0 as payment')])
            ->from('provider as p')
            ->leftJoin('provider_bill as pb', 'p.provider_id = pb.provider_id')
            ->leftJoin('bill_type as bt', 'pb.bill_type_id = bt.bill_type_id')
            ->groupBy(['p.provider_id', 'p.name']);
        ;

        $qPayment = (new Query())
            ->select(['p.provider_id', 'p.name', new Expression('0 AS debt'), new Expression('sum(pp.amount) as payment')])
            ->from('provider as p')
            ->leftJoin('provider_payment as pp', 'pp.provider_id = p.provider_id')
            ->groupBy(['p.provider_id', 'p.name']);
        ;

        if($toDate) {
            $qBill->andWhere(['<=', 'pb.date', $toDate]);
            $qPayment->andWhere(['<=', 'pp.date', $toDate]);
        }

        $qBill->union($qPayment, true);

        $query = (new Query())
            ->select(['provider_id', 'name', new Expression('sum(debt) as debt'), new Expression('sum(payment) as payment'),
                new Expression('sum(debt) - sum(payment) as balance')
            ])
            ->from(['a'=>$qBill])
            ->groupBy(['provider_id', 'name'])
            ->having(new Expression('round((sum(debt) - sum(payment))) <> 0'));



        if($this->provider_id) {
            $query->andWhere(['provider_id' => $this->provider_id]);
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function accountTotalBills()
    {

        $query = ProviderBill::find();
        $query->joinWith(['billType']);

        $query->where([
            'provider_id' => $this->provider_id,
        ]);

        $query->addSelect(['bill_type.multiplier']);

        return $query->sum('total * multiplier');
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function accountTotalPayed()
    {

        $query = ProviderPayment::find();

        $query->where([
            'provider_id' => $this->provider_id,
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
            $toDate = (new \DateTime('first day of this month'))->format('d-m-Y');
        }
        if (empty($fromDate)){
            $fromDate = (new \DateTime('last day of this month'))->format('d-m-Y');
        }

        $toDate = Yii::$app->getFormatter()->asDate($toDate, 'yyyy-MM-dd');
        $fromDate = Yii::$app->getFormatter()->asDate($fromDate, 'yyyy-MM-dd');


        $subQuery = new Query();
        $subQuery
            ->select(['provider_id', 'sum(pb.total * pt.multiplier) AS billed'])
            ->from('provider_bill pb')
            ->leftJoin('bill_type pt','pb.bill_type_id = pt.bill_type_id')
            ->andWhere(['and', ['>=', 'pb.date', $fromDate], ['<=', 'pb.date', $toDate] ])
            ->groupBy(['provider_id'])
        ;


        $query = new Query();
        $query
            ->select(['p.provider_id', 'p.name', 'pb.billed as facturado', 'sum(pp.amount) as pagos'])
            ->from('provider p')
            ->leftJoin(['pb'=>$subQuery], 'p.provider_id = pb.provider_id')
            ->leftJoin('provider_payment pp', 'p.provider_id = pp.provider_id')
            ->groupBy(['p.provider_id', 'p.name'])
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