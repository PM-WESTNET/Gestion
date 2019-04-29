<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/04/18
 * Time: 12:08
 */

namespace app\modules\westnet\reports\services;


use app\components\helpers\ExcelExporter;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\westnet\reports\models\HistoricActiveContracts;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\search\ReportSearch;
use yii\db\Expression;
use yii\db\Query;

class ReportDataService
{
    /**
     * Grabo el valor
     *
     * @param $report
     * @param $fecha
     * @param $value
     */
    private function save($report, $fecha, $value)
    {
        $rd = new ReportData();
        $rd->report = $report;
        $rd->period = $fecha;
        $rd->value  = $value;
        $rd->save();
    }
    /**
     * Guardo las conexiones activas en este periodo.
     *
     * @param \DateTime $fecha
     * @throws \Exception
     */
    public function saveConexionesActivas($fecha)
    {
        try {
            $value = (new ReportSearch())->countActiveContracts($fecha);
            $this->save(ReportData::REPORT_ACTIVE_CONNECTION, $fecha->format('Ym'), $value);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param \DateTime $periodo
     * @throws \Exception
     */
    public function savePasivo(\DateTime $periodo)
    {
        try {
            $search = new CustomerSearch();
            $saldo = $search->buildDebtorsQuery([
                'CustomerSearch' => [
                    'toDate' => $periodo->modify('last day of this month')->format('Y-m-d')
                ]
            ])->sum('saldo');

            $query = new Query();
            $facturado = $query
                ->select(new Expression('sum(b.total * bt.multiplier) as facturado'))
                ->from('bill b')
                ->leftJoin('bill_type bt', 'b.bill_type_id = bt.bill_type_id')
                ->where(new Expression('date_format(b.date, \'%Y-%m\') = \''. $periodo->format('Y-m').'\''))
                ->scalar()
            ;

            error_log( $periodo->format('Ym'). ' - facturado: ' .  $facturado . " - saldo: " . $saldo . " - value: " . ($saldo/$facturado) );

            $value =  ($facturado==null ? 0 : (round(($saldo/$facturado) * 100,2)) ) ;
            $this->save(ReportData::REPORT_COMPANY_PASSIVE, $periodo->format('Ym'), $value);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param \DateTime $periodo
     * @throws \Exception
     */
    public function saveDebtBills(\DateTime $periodo, $report)
    {
        try {
            $search = new CustomerSearch();

            $cant = substr($report, -1);

            error_log($search->buildDebtorsQuery([
                'CustomerSearch' => [
                    'toDate' => $periodo->modify('last day of this month')->format('Y-m-d'),
                    'debt_bills_from' => $cant,
                    'debt_bills_to' =>  $cant
                ]
            ])->select(['count(*)'])->createCommand()->getRawSql());

            $value = 0;
            $value = $search->buildDebtorsQuery([
                'CustomerSearch' => [
                    'toDate' => $periodo->modify('last day of this month')->format('Y-m-d'),
                    'debt_bills_from' => $cant,
                    'debt_bills_to' =>  $cant
                ]
            ])->count('saldo');

            $rp = ReportData::findOne(['report'=>ReportData::REPORT_ACTIVE_CONNECTION, 'period'=>$periodo->format('Ym')]);
            if($rp) {
                $value = ($value*100)/$rp->value;
            }
            error_log($periodo->format('Ym'));
            $this->save($report, $periodo->format('Ym'), round($value,2));

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function saveUpDown(\DateTime $period)
    {
        try {
            $data = (new ReportSearch())->findCustomerVariationPerMonth([]);
            foreach($data as $item) {
                error_log($item['periodo'] . " - " . $period->format('Y-m') );
                if($item['periodo'] == $period->format('Y-m')) {
                    $this->save(ReportData::REPORT_UP, $period->format('Ym'), $item['alta']);
                    $this->save(ReportData::REPORT_DOWN, $period->format('Ym'), $item['baja']);
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}