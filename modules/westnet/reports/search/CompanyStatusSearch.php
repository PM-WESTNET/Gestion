<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 15/08/17
 * Time: 12:02
 */

namespace app\modules\westnet\reports\search;


use app\modules\westnet\models\DebtEvolution;
use app\modules\westnet\reports\ReportsModule;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class CompanyStatusSearch extends Model
{
    public $date_from;
    public $date_to;

    public function init()
    {
        parent::init();
        $this->date_from = (new \DateTime('first day of this month'))->format('01-01-Y');
        $this->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['date_from', 'date_to'], 'string'],
            [['date_from', 'date_to'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => ReportsModule::t('app', 'Date From'),
            'date_to' => ReportsModule::t('app', 'Date To'),
        ];
    }


    public function findPerMonthByDate($param)
    {

        $this->load($param);

        $queryBill = new Query();
        $queryBill
            ->select(['b.date as fecha', new Expression('(b.total * bt.multiplier) AS facturado'), new Expression('0 as cobros')])
            ->from(['customer cus'])
            ->leftJoin('bill b', 'cus.customer_id = b.customer_id')
            ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
        ;
        $queryPayment = new Query();
        $queryPayment
            ->select(['p.date as fecha', new Expression('0 AS facturado'), 'p.amount AS cobros'])
            ->from(['customer cus'])
            ->leftJoin('payment p', 'cus.customer_id = p.customer_id')
        ;

        if($this->date_from) {
            $queryBill->andWhere(['>=', 'b.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
            $queryPayment->andWhere(['>=', 'p.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if($this->date_to) {
            $queryBill->andWhere(['<=', 'b.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
            $queryPayment->andWhere(['<=', 'p.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        $queryBill->union($queryPayment, true);
        $query = new Query();
        $query
            ->select([
                new Expression('date_format(fecha, \'%m/%Y\') as date'), new Expression('round(sum(facturado)) as facturado'),
                new Expression('round(sum(cobros)) as cobros'), new Expression('round(sum(cobros)  - sum(facturado)) as diferencia')
            ])
            ->from(['a' => $queryBill])
            ->groupBy([new Expression('date_format(fecha, \'%m/%Y\')')])
            ->orderBy(['date_format(fecha, \'%Y%m\')' =>SORT_ASC])
        ;

        return $query->all();
    }



    public function debtEvolution($param)
    {

        $this->load($param);

        $query = DebtEvolution::find();
        $query
            ->select(['*'])
            ->from(['debt_evolution de'])
        ;

        if($this->date_from) {
            $query->andWhere(['>=', 'de.period', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if($this->date_to) {
            $query->andWhere(['<=', 'de.period', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        return $query->all();
    }
}