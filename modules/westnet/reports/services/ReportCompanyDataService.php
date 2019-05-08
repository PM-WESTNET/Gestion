<?php

namespace app\modules\westnet\reports\services;

use app\modules\sale\models\search\CustomerSearch;
use app\modules\westnet\reports\models\HistoricActiveContracts;
use app\modules\westnet\reports\models\ReportCompanyData;
use app\modules\westnet\reports\search\ReportCompanySearch;

class ReportCompanyDataService
{
    /**
     * Grabo el valor
     *
     * @param $report
     * @param $fecha
     * @param $value
     * @param $company_id optional
     */
    private function save($report, $fecha, $value, $company_id = null)
    {
        $rd = new ReportCompanyData();
        $rd->report = $report;
        $rd->period = $fecha;
        $rd->value  = $value;
        $rd->company_id = $company_id;
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
            $companies_contract_qty = (new ReportCompanySearch())->countActiveContracts($fecha);

            foreach ($companies_contract_qty as $company_id => $qty) {
                if($company_id) {
                    $this->save(ReportCompanyData::REPORT_ACTIVE_CONNECTION, $fecha->format('Ym'), $qty, $company_id);
                }
            }
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

            $debt_query = [];
            $debt_query = $search->buildDebtorsQuery([
                'CustomerSearch' => [
                    'toDate' => $periodo->modify('last day of this month')->format('Y-m-d'),
                    'debt_bills_from' => $cant,
                    'debt_bills_to' =>  $cant
                ]
            ])->all();

            //relleno el array con porcentaje diferenciado por empresa
            $companies_debt_qty = [];
            foreach ($debt_query as $company_debt_qty) {
                if(array_key_exists('customer_company', $companies_debt_qty)) {
                    $companies_debt_qty[$company_debt_qty['customer_company']] = (array_key_exists($company_debt_qty['customer_company'], $companies_debt_qty) ? $companies_debt_qty['customer_company'] : 0 ) + 1 ;
                }
            }

            //Guardo los registros en la tabla
            foreach ($companies_debt_qty as $company_id => $qty) {
                $rp = ReportCompanyData::findOne(['report' => ReportCompanyData::REPORT_ACTIVE_CONNECTION, 'period' => $periodo->format('Ym'), 'company_id' => $company_id]);
                if($rp) {
                    $qty = ($qty*100)/$rp->value;
                }

                error_log($periodo->format('Ym'));
                $this->save($report, $periodo->format('Ym'), round($qty,2), $company_id);
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param \DateTime $period
     * @throws \Exception
     * Guarda las bajas y altas de los clientes
     */
    public function saveUpDown(\DateTime $period)
    {
        try {
            $data = (new ReportCompanySearch())->findCustomerVariationPerMonth([]);
            foreach($data as $item) {
                error_log($item['periodo'] . " - " . $period->format('Y-m') . " - " . 'Company: '. $item['company_id']);
                if($item['periodo'] == $period->format('Y-m')) {
                    $this->save(ReportCompanyData::REPORT_UP, $period->format('Ym'), $item['alta'], $item['company_id']);
                    $this->save(ReportCompanyData::REPORT_DOWN, $period->format('Ym'), $item['baja'], $item['company_id']);
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}