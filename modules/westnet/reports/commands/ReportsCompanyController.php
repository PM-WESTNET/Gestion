<?php

namespace app\modules\westnet\reports\commands;

use app\modules\westnet\reports\models\ReportCompanyData;
use app\modules\westnet\reports\services\ReportCompanyDataService;
use yii\console\Controller;

class ReportsCompanyController extends Controller
{

    /**
     * Guarda las conexiones activas al dia de hoy para el periodo actual.
     *
     */
    public function actionActiveConnections($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportCompanyDataService();

        if($fromBeginning) {
            ReportCompanyData::deleteAll(['report'=> ReportCompanyData::REPORT_ACTIVE_CONNECTION]);
            $date = new \DateTime('2010-01-31');
            do {
                try {
                    $rs->saveConexionesActivas($date);
                } catch (\Exception $ex) {
                    error_log($ex->getMessage());
                }
                $date->modify('last day of next month');
            } while ($date->format('Ymd') <= (new \DateTime('now'))->format('Ymd'));

        } else {
            if($theDate) {
                $date = new \DateTime($theDate);
            } else {
                $date = new \DateTime('last day of this month'); //new \DateTime('2010-01-31');
                if(!$this->isLastDayOfMonth()){
                    return;
                }
            }

            try {
                $rs->saveConexionesActivas($date);
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }

    /**
     * Guarda el porcentaje de facturas adeudadas
     */
    public function actionDebtBills($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportCompanyDataService();
        if($fromBeginning) {
            ReportCompanyData::deleteAll(['report' => ReportCompanyData::REPORT_DEBT_BILLS_1]);
            ReportCompanyData::deleteAll(['report' => ReportCompanyData::REPORT_DEBT_BILLS_2]);
            ReportCompanyData::deleteAll(['report' => ReportCompanyData::REPORT_DEBT_BILLS_3]);
            ReportCompanyData::deleteAll(['report' => ReportCompanyData::REPORT_DEBT_BILLS_4]);

            $date = new \DateTime('2018-01-31');
            do {
                try {
                    $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_1);
                    $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_2);
                    $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_3);
                    $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_4);
                } catch (\Exception $ex) {
                    error_log($ex->getMessage());
                    error_log($ex->getFile() . " - " . $ex->getLine());
                }
                $date->modify('last day of next month');
            } while ($date->format('Ymd') <= (new \DateTime('now'))->format('Ymd'));
        } else {
            if($theDate) {
                $date = new \DateTime($theDate);
            } else {
                $date = new \DateTime('last day of this month'); //new \DateTime('2010-01-31');
                if(!$this->isLastDayOfMonth()){
                    return;
                }
            }

            try {
                $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_1);
                $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_2);
                $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_3);
                $rs->saveDebtBills($date, ReportCompanyData::REPORT_DEBT_BILLS_4);
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }

    /**
     * Guarda las altas y bajas de los clientes
     */
    public function actionUpAndDown($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportCompanyDataService();
        if($fromBeginning) {
            ReportCompanyData::deleteAll(['report'=>ReportData::REPORT_UP]);
            ReportCompanyData::deleteAll(['report'=>ReportData::REPORT_DOWN]);

            $date = new \DateTime('2010-01-31');
            do {
                try {
                    $rs->saveUpDown($date);
                } catch (\Exception $ex) {
                    error_log($ex->getMessage());
                }
                $date->modify('last day of next month');
            } while ($date->format('Ymd') <= (new \DateTime('now'))->format('Ymd'));
        } else {
            if($theDate) {
                $date = new \DateTime($theDate);
            } else {
                $date = new \DateTime('last day of this month'); //new \DateTime('2010-01-31');
                if(!$this->isLastDayOfMonth()){
                    return;
                }
            }
            try {
                $rs->saveUpDown($date);

            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }

    /**
     * Verifica que hoy sea el ultimo dia del mes
     */
    private function isLastDayOfMonth()
    {
        return (new \DateTime('last day of this month'))->format('Ymd') == (new \DateTime('now'))->format('Ymd');
    }
}