<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:46
 */

namespace app\modules\westnet\reports\commands;

use app\modules\accounting\behaviors\AccountMovementBehavior;
use app\modules\checkout\models\Payment;
use app\modules\config\models\Config;
use app\modules\paycheck\models\Paycheck;
use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderPayment;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\models\Node;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\services\ReportDataService;
use Yii;
use yii\base\Event;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

class ReportsController extends Controller
{

    /**
     * Guarda las conexiones activas al dia de hoy para el periodo actual.
     *
     */
    public function actionActiveConnections($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportDataService();
        if($fromBeginning) {
            ReportData::deleteAll(['report'=>ReportData::REPORT_ACTIVE_CONNECTION]);
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

    public function actionCompanyPassive($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportDataService();
        if($fromBeginning) {
            ReportData::deleteAll(['report'=>ReportData::REPORT_COMPANY_PASSIVE]);

            // Inserto los historicos
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201111,42.86)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201112,40.07)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201201,37.38)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201202,33.93)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201203,25.95)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201204,28.50)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201205,20.29)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201206,16.37)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201207,15.90)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201208,16.16)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201209,21.20)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201210,17.80)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201211,18.76)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201212,18.70)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201301,18.85)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201302,18.66)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201303,16.93)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201304,18.53)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201305,14.31)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201306,15.60)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201307,12.13)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201308,11.40)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201309,16.59)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201310,19.30)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201311,20.05)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201312,17.36)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201401,15.76)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201402,18.40)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201403,15.27)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201404,12.86)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201405,12.77)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201406,13.47)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201407,10.56)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201408,13.50)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201409,11.60)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201410,25.26)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201411,27.96)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201412,27.00)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201501,27.00)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201502,14.73)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201503,13.79)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201504,11.20)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201505,10.85)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201506,10.64)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201507,11.33)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201508,10.43)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201509,11.73)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201510,12.52)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201511,11.41)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201512,12.78)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201601,14.48)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201602,14.48)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201603,13.60)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201604,13.43)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201605,14.57)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201606,14.55)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201607,0)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201608,0.00)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201609,0.00)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201610,0.00)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201611,8.45)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201612,5.07)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201701,10.27)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201702,9.52)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201703,4.40)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201704,4.49)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201705,8.38)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201706,6.58)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201707,11.25)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201708,6.62)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201709,6.13)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201710,4.61)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201711,6.51)")->execute();
            Yii::$app->db->createCommand("INSERT INTO report_data (report, period, `value`) values('company_passive', 201712,7.50)")->execute();

            $date = new \DateTime('2018-01-31');
            do {
                try {
                    $rs->savePasivo($date);
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
                $rs->savePasivo($date);
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }

    public function actionDebtBills($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportDataService();
        if($fromBeginning) {
            ReportData::deleteAll(['report'=>ReportData::REPORT_DEBT_BILLS_1]);
            ReportData::deleteAll(['report'=>ReportData::REPORT_DEBT_BILLS_2]);
            ReportData::deleteAll(['report'=>ReportData::REPORT_DEBT_BILLS_3]);
            ReportData::deleteAll(['report'=>ReportData::REPORT_DEBT_BILLS_4]);

            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201112,24.90)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201201,18.21)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201202,19.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201203,14.14)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201204,18.25)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201205,14.71)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201206,14.07)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201207,12.73)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201208,11.66)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201209,23.59)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201210,8.80)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201211,10.23)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201212,8.74)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201301,8.74)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201302,10.70)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201303,7.83)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201304,8.92)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201305,6.96)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201306,8.52)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201307,6.53)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201308,6.93)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201309,8.23)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201310,17.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201311,7.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201312,7.43)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201401,7.57)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201402,8.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201403,6.95)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201404,6.81)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201405,6.19)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201406,6.70)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201407,5.54)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201408,6.46)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201409,6.33)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201410,15.57)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201411,8.22)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201412,6.24)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201501,6.24)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201502,7.37)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201503,6.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201504,5.83)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201505,5.69)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201506,4.61)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201507,5.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201508,5.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201509,4.82)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201510,4.19)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201511,5.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201512,5.47)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201601,5.63)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201602,6.38)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201603,5.97)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201604,4.32)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201605,4.60)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201606,4.87)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201607,4.87)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201608,3.81)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201609,5.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201610,3.92)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201611,4.28)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201612,3.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201701,3.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201702,4.89)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201703,3.66)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201704,4.18)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201705,5.80)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201706,4.25)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201707,9.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201708,3.33)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201709,4.28)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201710,3.43)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201711,3.58)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201712,4.14)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201801,4.11)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201802,5.40)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_1', 201803,3.44)")->execute();

            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201112, 6.96)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201201, 6.30)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201202, 5.30)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201203, 4.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201204, 4.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201205, 4.23)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201206, 4.08)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201207, 3.57)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201208, 2.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201209, 2.14)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201210, 3.69)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201211, 2.87)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201212, 3.24)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201301, 3.24)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201302, 2.52)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201303, 3.06)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201304, 2.63)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201305, 2.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201306, 1.99)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201307, 2.11)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201308, 1.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201309, 2.25)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201310, 2.40)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201311, 3.46)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201312, 2.30)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201401, 2.20)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201402, 2.44)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201403, 2.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201404, 1.91)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201405, 1.97)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201406, 1.93)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201407, 1.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201408, 1.64)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201409, 1.76)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201410, 2.35)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201411, 3.87)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201412, 1.94)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201501, 1.94)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201502, 1.97)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201503, 1.91)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201504, 1.67)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201505, 1.50)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201506, 1.37)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201507, 0.73)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201508, 1.08)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201509, 1.28)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201510, 1.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201511, 0.98)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201512, 1.50)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201601, 1.59)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201602, 1.56)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201603, 1.75)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201604, 1.19)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201605, 1.29)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201606, 1.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201607, 1.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201608, 1.35)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201609, 1.54)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201610, 1.35)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201611, 1.26)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201612, 1.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201701, 1.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201702, 1.19)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201703, 0.48)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201704, 0.59)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201705, 0.45)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201706, 0.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201707, 0.68)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201708, 0.65)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201709, 0.31)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201710, 0.30)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201711, 0.62)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201712, 1.14)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201801, 0.83)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201802, 0.92)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_2', 201803, 1.03)")->execute();

            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201112, 1.77)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201201, 2.46)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201202, 2.36)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201203, 1.27)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201204, 1.14)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201205, 0.72)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201206, 0.42)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201207, 0.35)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201208, 0.33)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201209, 0.44)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201210, 0.47)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201211, 0.50)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201212, 0.64)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201301, 0.64)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201302, 0.43)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201303, 0.73)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201304, 0.81)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201305, 0.29)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201306, 0.67)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201307, 0.07)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201308, 0.26)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201309, 0.75)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201310, 1.42)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201311, 1.23)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201312, 0.98)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201401, 0.21)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201402, 0.74)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201403, 0.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201404, 0.53)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201405, 0.52)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201406, 0.65)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201407, 0.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201408, 0.32)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201409, 0.28)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201410, 1.20)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201411, 1.73)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201412, 1.10)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201501, 1.10)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201502, 0.37)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201503, 0.62)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201504, 0.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201505, 0.41)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201506, 0.59)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201507, 0.35)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201508, 0.25)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201509, 0.48)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201510, 0.39)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201511, 0.53)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201512, 0.55)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201601, 0.80)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201602, 0.87)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201603, 0.59)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201604, 0.92)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201605, 0.70)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201606, 0.52)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201607, 0.52)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201608, 0.16)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201609, 0.62)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201610, 0.38)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201611, 0.22)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201612, 0.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201701, 0.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201702, 0.10)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201703, 0.04)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201704, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201705, 0.10)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201706, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201707, 0.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201708, 0.04)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201709, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201710, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201711, 0.08)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201712, 0.01)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201801, 0.50)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201802, 0.00)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_3', 201803, 0.02)")->execute();

            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201112, 0.34)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201201, 0.68)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201202, 0.56)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201203, 0.98)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201204, 0.32)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201205, 0.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201206, 0.06)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201207, 0.08)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201208, 0.04)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201209, 0.04)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201210, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201211, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201212, 0.05)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201301, 0.05)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201302, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201303, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201304, 0.08)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201305, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201306, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201307, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201308, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201309, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201310, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201311, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201312, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201401, 0.15)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201402, 0.17)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201403, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201404, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201405, 0.04)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201406, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201407, 0.06)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201408, 0.26)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201409, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201410, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201411, 0.18)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201412, 0.05)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201501, 0.05)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201502, 0.05)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201503, 0.06)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201504, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201505, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201506, 0.11)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201507, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201508, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201509, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201510, 0.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201511, 0.11)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201512, 0.03)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201601, 0.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201602, 0.16)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201603, 0.09)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201604, 0.38)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201605, 0.57)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201606, 0.41)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201607, 0.41)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201608, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201609, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201610, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201611, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201612, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201701, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201702, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201703, 0.02)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201704, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201705, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201706, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201707, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201708, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201709, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201710, 0.00)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201711, 0.01)")->execute();
            Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201712, 0.05)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201801, 0.00)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201802, 0.00)")->execute();
            //Yii::$app->db->createCommand("insert into report_data (report, period, `value`) values('debt_bills_4', 201803, 0.00)")->execute();

            $date = new \DateTime('2018-01-31');
            do {
                try {
                    $rs->saveDebtBills($date, ReportData::REPORT_DEBT_BILLS_1);
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
                $rs->saveDebtBills($date, ReportData::REPORT_DEBT_BILLS_1);

            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }

    public function actionUpAndDown($fromBeginning=false, $theDate = null)
    {
        $rs = new ReportDataService();
        if($fromBeginning) {
            ReportData::deleteAll(['report'=>ReportData::REPORT_UP]);
            ReportData::deleteAll(['report'=>ReportData::REPORT_DOWN]);

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

    private function isLastDayOfMonth()
    {
        return (new \DateTime('last day of this month'))->format('Ymd') == (new \DateTime('now'))->format('Ymd');
    }
}