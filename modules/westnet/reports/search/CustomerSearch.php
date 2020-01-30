<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 15/08/17
 * Time: 12:02
 */

namespace app\modules\westnet\reports\search;


use app\components\helpers\DbHelper;
use app\modules\sale\models\Customer;
use app\modules\westnet\reports\ReportsModule;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class CustomerSearch extends Model
{

    const LAST_WEEK_RANGE= 'last_week';
    const LAST_MONTH_RANGE = 'last_month';
    const LAST_YEAR_RANGE = 'last_year';

    public $date_from;
    public $date_to;

    // Rango de tiempo para el reporte de clientes actualizados
    public $range;

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
            [['date_from', 'date_to', 'range'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_from' => ReportsModule::t('app', 'Date From'),
            'date_to' => ReportsModule::t('app', 'Date To'),
            'range' => Yii::t('app', 'Time Range')
        ];
    }


    public function findPerMonth($params)
    {
        $this->load($params);

        $query = new Query();
        $query
            ->select([new Expression('count(DISTINCT cus.customer_id) cant'), new Expression('date_format(con.date, \'%m/%Y\') as periodo')])
            ->from('customer cus')
            ->leftJoin('contract con', 'cus.customer_id = con.customer_id')
            ->andWhere(new Expression("con.status <> 'draft'"))
            ->andWhere(new Expression('(con.to_date is null or ( date_format(con.to_date, \'%d\') > 28 and date_format(con.to_date, \'%d\') <= 31 ) )'))
            ->groupBy([new Expression('date_format(con.date, \'%m/%Y\')')])
            ->orderBy(['date_format(con.date, \'%Y%m\')' =>SORT_ASC])
        ;

        $mainQuery = new Query();
        $mainQuery->select(new Expression('cant, coalesce(periodo, \'06/2002\') as periodo, @cant:=@cant + cant as total'))
                  ->from(['c'=>$query, '(SELECT @cant :=0) as tc'])
        ;

        return $mainQuery->all();
    }

    public function findDifferenceByMonth()
    {
/*
select periodo, sum(alta) as alta, sum(baja) as baja, sum(alta) - sum(baja) as diferencia from (
  SELECT
    date_format(c.from_date, '%Y-%m') AS periodo,
    count(*)                          AS alta,
    0                                 AS baja
  FROM contract c
  WHERE c.status = 'active'
  GROUP BY date_format(c.from_date, '%Y-%m')
  UNION ALL
  SELECT
    date_format(to_date, '%Y-%m') AS periodo,
    0                                baja,
    count(*)                      AS baja
  FROM contract
  WHERE status = 'low'
  GROUP BY date_format(to_date, '%Y-%m')
) t
GROUP BY periodo
;
 * */

    }

    /**
     * Retorno la cantidad de contratos dados de baja por motivo y fecha
     *
     * @param $param
     */
    public function findByLowReason($param)
    {
        $this->load($param);

        $query = new Query();
        $query
            ->select(['cat.name', new Expression('date_format(con.date, \'%m/%Y\') as period'), 'count(con.contract_id) as cant'])
            ->from('contract con')
            ->innerJoin( DbHelper::getDbName(Yii::$app->dbticket) .'.category as cat', 'con.category_low_id = cat.category_id')
            ->groupBy(['cat.name', new Expression('date_format(con.date, \'%m/%Y\')')])
            ->orderBy(['date_format(con.date, \'%Y%m\')' =>SORT_ASC])
        ;

        if($this->date_from) {
            $query->andWhere(['>=', 'con.date', (new \DateTime($this->date_from))->format('Y-m-d')]);
        }

        if($this->date_to) {
            $query->andWhere(['<=', 'con.date', (new \DateTime($this->date_to))->format('Y-m-d')]);
        }

        return $query->all();
    }


    /**
     * Datos para reporte de Clientes Actualizdos
     */
    public function findByCustomersUpdated($params)
    {
        $this->load($params);

        $from_date = null;
        $to_date = null;
        $labels = [];
        $points = [];

        /**
         * Según el rango calculo la fecha mínima y máxima
         * Por cada fecha que se muestra en el gráfico cuento los clientes actualizados entre el punto anterior y el actual
         */
        switch($this->range) {
            case self::LAST_WEEK_RANGE:
                $from_date = (new \DateTime())->modify('-7 days');
                $to_date = (new \DateTime());

                for ($day= $from_date->getTimestamp(); $day <= $to_date->getTimestamp(); $day = $day + 86400) {
                    $labels[] = Yii::$app->formatter->asDate($day, 'dd/MM');
                    $qty = Customer::find()->andWhere(['last_update' => Yii::$app->formatter->asDate($day, 'yyyy-MM-dd')])->count();
                    $points[] = [
                        'x' => Yii::$app->formatter->asDate($day, 'dd/MM'),
                        'y' => $qty
                    ];
                }

                break;
            case self::LAST_MONTH_RANGE:
                $from_date = (new \DateTime())->modify('-30 days');
                $to_date = (new \DateTime());
                $before = null;
                for ($month= $from_date->getTimestamp(); $month <= $to_date->getTimestamp(); $month = $month + (86400 * 5)) {
                    $labels[] = Yii::$app->formatter->asDate($month, 'dd/MM');
                    $qty = Customer::find()
                        ->andWhere(['<=', 'last_update', Yii::$app->formatter->asDate($month, 'yyyy-MM-dd')])
                        ->andFilterWhere(['>=', 'last_update', $before])
                        ->count();
                    $points[] = [
                        'x' => Yii::$app->formatter->asDate($month, 'dd/MM'),
                        'y' => $qty
                    ];

                    $before = Yii::$app->formatter->asDate($month, 'yyyy-MM-dd');
                }

                break;
            case self::LAST_YEAR_RANGE:
                $from_date = (new \DateTime())->modify('-1 year');
                $to_date = (new \DateTime());

                $before = null;

                for ($year= $from_date->getTimestamp(); $year <= $to_date->getTimestamp(); $year=$year + (86400 * 30)) {
                    $labels[] = Yii::$app->formatter->asDate($year, 'MM/yyyy');

                    $qty = Customer::find()
                        ->andWhere(['<=', 'last_update', Yii::$app->formatter->asDate($year, 'yyyy-MM-dd')])
                        ->andFilterWhere(['>=', 'last_update', $before])
                        ->count();

                    $points[] = [
                        'x' => Yii::$app->formatter->asDate($year, 'dd/MM'),
                        'y' => $qty
                    ];

                    $before = Yii::$app->formatter->asDate($year, 'yyyy-MM-dd');
                }
                break;
            default :
                $from_date = (new \DateTime())->modify('-7 days');
                $to_date = (new \DateTime());

//                var_dump($from_date->format('d-m-Y'));
//                die();

                for ($day= $from_date->getTimestamp(); $day <= $to_date->getTimestamp(); $day=86400 + $day) {
                    //var_dump($day);

                    $labels[] = Yii::$app->formatter->asDate($day, 'dd/MM');
                    $qty = Customer::find()->andWhere(['last_update' => Yii::$app->formatter->asDate($day, 'yyyy-MM-dd')])->count();
                    $points[] = [
                        'x' => Yii::$app->formatter->asDate($day, 'dd/MM'),
                        'y' => $qty
                    ];
                }

                break;
        }
        //die();
        return [
            'labels' => $labels,
            'points' => $points,
        ];


    }


}