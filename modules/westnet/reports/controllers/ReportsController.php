<?php

namespace app\modules\westnet\reports\controllers;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\search\UserAppActivitySearch;
use app\modules\westnet\models\search\ConnectionForcedHistorialSearch;
use app\modules\westnet\models\search\NotifyPaymentSearch;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\ReportsModule;
use app\modules\westnet\reports\search\CustomerSearch;
use app\modules\westnet\reports\search\ReportSearch;
use Yii;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;

/**
 * CustomerController
 * Implementa los reportes relacionados con clientes.
 *
 */
class ReportsController extends Controller
{

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionCustomersPerMonth()
    {
        $search = new ReportSearch();
        $data = $search->findReportDataActiveContracts((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item->period . '01');
            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols[] = $date->format('m-Y');
                $datas[] = $item->value;
            }
        }

        return $this->render('/reports/customer-per-month', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas
        ]);
    }


    public function actionCostumerVariationPerMonth()
    {
        $search = new ReportSearch();
        $data = $search->findCustomerVariationPerMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];
        $colors = [];
        $old_value = null;
        $old_period = null;

        foreach ($data as $item) {
            $date = new \DateTime($item['periodo'] . '-01');

            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols[] = $date->format('m-Y');
                $datas[] = $item['diferencia'];

                $colors[] = (($item['diferencia'] > 0) ? 'green' : 'red');
            }
        }

        return $this->render('/reports/costumer-variation-per-month', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas,
            'colors' => $colors
        ]);
    }

    public function actionCompanyPassive()
    {
        $search = new ReportSearch();
        $data = $search->findReportDataCompanyPassive((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];

        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . '01');

            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols[] = $date->format('m-Y');
                $datas[] = $item['value'];
            }
        }

        return $this->render('/reports/company-passive', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas,
        ]);
    }

    public function actionDebtBills()
    {
        $search = new ReportSearch();
        $data1 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_1);
        $data2 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_2);
        $data3 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_3);
        $data4 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_4);

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $process = function ($data) use ($from, $to) {
            $cols = [];
            $datas = [];

            foreach ($data as $item) {
                $date = new \DateTime($item['period'] . '01');

                if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                    $cols[] = $date->format('m-Y');
                    $datas[] = $item['value'];
                }
            }
            return [
                'cols' => $cols,
                'data' => $datas
            ];
        };


        return $this->render('/reports/debt-bills', [
            'model' => $search,
            'data1' => $process($data1),
            'data2' => $process($data2),
            'data3' => $process($data3),
            'data4' => $process($data4),
        ]);
    }

    public function actionLowByMonth()
    {
        $search = new ReportSearch();
        $data = $search->findLowByMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . '-01');

            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols[] = $date->format('m-Y');
                $datas[] = $item['porcentage'];
            }
        }

        return $this->render('/reports/low-by-month', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas
        ]);
    }

    /**
     * Lista las razones de baja
     * @return string
     */
    public function actionLowByReason()
    {
        $search = new ReportSearch();
        $data = $search->findLowByReasonMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $dataset = [];
        $labels = [];
        $datas = [];
        $scales = [];
        $category_low_id = null;
        $category = null;
        $i = 1;
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . '-01');
            if ($category_low_id != $item['category_low_id'] && $category_low_id != null) {
                $dataset[] = [
                    'label' => $category,
                    'data' => $datas,
                    'fill' => false,
                    'backgroundColor' => sprintf('rgba(%s,%s,%s,1)', rand(1, 255), rand(1, 255), rand(1, 255)),
                ];
                $i++;
                $datas = [];
            }
            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                if (array_search($date->format('m-Y'), $labels) === false) {
                    $labels[] = $date->format('m-Y');
                }
                $datas[] = $item['porcentage'];
            }

            $category_low_id = $item['category_low_id'];
            $category = $item['name'];

        }
        if ($category_low_id != $item['category_low_id'] && $category_low_id != null) {
            $dataset[] = [
                'label' => $category,
                'data' => $datas,
                'fill' => false,
                'backgroundColor' => sprintf('rgba(%s,%s,%s,1)', rand(1, 255), rand(1, 255), rand(1, 255)),
                'yAxisID' => 'y-axis-' . $i
            ];
        }
        $funcion = function ($a, $b) {
            return (new \DateTime('01-' . $a))->format('Ymd') - (new \DateTime('01-' . $b))->format('Ymd');
        };

        usort($labels, $funcion);

        return $this->render('/reports/low-by-reason', [
            'model' => $search,
            'labels' => $labels,
            'dataset' => $dataset,
        ]);
    }

    /**
     * Rentabilidad
     *
     * @return string
     */
    public function actionCostEffectiveness()
    {
        $search = new ReportSearch();
        $data = $search->findCostEffectiveness((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $labels = [];
        $rentabilidad = [];
        $datasets = [];
        $earn = 0;
        $outgo = 0;
        $account_movements = 0;
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . "-01");
            $earn += $item['facturado'];
            $outgo += $item['pagos'];
            $account_movements += $item['pagos_account'];
            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $labels[] = $date->format('m-Y');
                $rentabilidad[] = round(($item['diferencia'] / $item['facturado']) * 100, 2);
            }
        }

        $datasets[] = [
            'label' => ReportsModule::t('app', 'Effectiveness'),
            'data' => $rentabilidad,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];

        return $this->render('/reports/cost-effectiveness', [
            'model' => $search,
            'labels' => $labels,
            'datasets' => $datasets,
            'earn' => $earn,
            'outgo' => $outgo,
            'account_movements' => $account_movements
        ]);
    }

    /**
     * Reporte de ingresos por forma de pago
     *
     * @return string
     */
    public function actionPaymentMethods()
    {
        $search = new ReportSearch();

        //Utiliza por defecto el primer dìa del mes actual
        $search->date_from = (new \DateTime('first day of this month'))->format('d-m-Y');

        $data = $search->findPaymentsMethod((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $labels = [];
        $payments = [];
        $datasets = [];

        foreach ($data as $item) {
            $labels[] = $item['payment_name'];
            $payments[] = round(($item['facturado']), 0);
        }

        $datasets[] = [
            'label' => ReportsModule::t('app', 'Payment Methods'),
            'data' => $payments,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];

        return $this->render('/reports/payment-methods', [
            'model' => $search,
            'labels' => $labels,
            'datasets' => $datasets,
            'payments' => $data,
        ]);
    }

    public function actionUpDownVariation()
    {
        $search = new ReportSearch();
        $data = $search->findUpsAndDowns((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $labels = [];
        $datasets = [];
        $altas = [];
        $bajas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . "01");

            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $labels[] = $date->format('m-Y');
                $altas[] = $item['up'];
                $bajas[] = $item['down'];
            }
        }

        $datasets[] = [
            'label' => ReportsModule::t('app', 'Up'),
            'data' => $altas,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];
        $datasets[] = [
            'label' => ReportsModule::t('app', 'Down'),
            'data' => $bajas,
            'fill' => false,
            'backgroundColor' => 'rgba(0,255,0,1)',
        ];

        return $this->render('/reports/up-down-variation', [
            'model' => $search,
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    public function actionInOut()
    {
        $search = new ReportSearch();
        if (!isset($_GET['ReportSearch'])) {
            $search->date_from = (new \DateTime('first day of this month'))->format('01-m-Y');
            $search->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
        }

        $query = $search->findInOut(Yii::$app->request->queryParams);
        $dataModel = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        $movementsModel = new ActiveDataProvider([
            'query' => $search->findMovements(Yii::$app->request->queryParams)
        ]);

        $total1 = $search->findInOutTotals(Yii::$app->request->queryParams);
        $totalCobrado = $total1['cobrado'];
        $totalPagado = $total1['pagado'];


        return $this->render('/reports/in-out', [
            'searchModel' => $search,
            'data'  => $dataModel,
            'movements' => $movementsModel,
            'totalCobrado' => $totalCobrado,
            'totalPagado' => $totalPagado,
        ]);
    }

    public function actionDashboard()
    {
        $search = new ReportSearch();
        $data = $search->findReportDataActiveContracts((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols_company_passive = [];
        $data_company_passive = [];
        foreach($data as $item){
            $date = new \DateTime($item->period.'01');
            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols_company_passive[] = $date->format('m-Y');
                $data_company_passive[] = $item->value;
            }
        }

        $data_cvpm = $search->findCustomerVariationPerMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $cols_cvpm = [];
        $data_cvpm = [];
        $colors_cvpm = [];
        $old_value = null;
        $old_period = null;

        foreach($data_cvpm as $item){
            $date = new \DateTime($item['periodo'].'-01');

            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols_cvpm[] = $date->format('m-Y');
                $data_cvpm[] = $item['diferencia'];

                $colors_cvpm[] = (($item['diferencia'] > 0) ? 'green': 'red' );
            }
        }

        $data_cp = $search->findReportDataCompanyPassive((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $cols_cp = [];
        $datas_cp = [];

        foreach ($data_cp as $item) {
            $date = new \DateTime($item['period'] . '01');

            if ($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols_cp[] = $date->format('m-Y');
                $datas_cp[] = $item['value'];
            }
        }

        $data1 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_1);
        $data2 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_2);
        $data3 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_3);
        $data4 = $search->findReportDataDebtBills((!Yii::$app->request->isPost) ? null : Yii::$app->request->post(), ReportData::REPORT_DEBT_BILLS_4);

        $process = function($data) use ($from, $to) {
            $cols = [];
            $datas = [];

            foreach($data as $item){
                $date = new \DateTime($item['period'].'01');

                if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                    $cols[] = $date->format('m-Y');
                    $datas[] = $item['value'];
                }
            }
            return [
                'cols' => $cols,
                'data' => $datas
            ];
        };

        $data_lbm = $search->findLowByMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $cols_lbm = [];
        $datas_lbm = [];
        foreach($data_lbm as $item){
            $date = new \DateTime($item['period'].'-01');

            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $cols_lbm[] = $date->format('m-Y');
                $datas_lbm[] = $item['porcentage'];
            }
        }

        $data_lbr = $search->findLowByReasonMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $dataset_lbr = [];
        $labels_lbr = [];
        $datas_lbr = [];
        $scales_lbr = [];
        $category_low_id = null;
        $category = null;
        $i = 1;
        foreach($data_lbr as $item){
            $date = new \DateTime($item['period'].'-01');
            if( $category_low_id != $item['category_low_id'] && $category_low_id != null ) {
                $dataset_lbr[] = [
                    'label' => $category,
                    'data' => $datas_lbr,
                    'fill' => false,
                    'backgroundColor' => sprintf('rgba(%s,%s,%s,1)', rand(1,255), rand(1,255), rand(1,255)),
                ];
                $i++;
                $datas_lbr = [];
            }
            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                if(array_search($date->format('m-Y'), $labels_lbr)===false) {
                    $labels[] = $date->format('m-Y');
                }
                $datas_lbr[] = $item['porcentage'];
            }

            $category_low_id = $item['category_low_id'];
            $category = $item['name'];

        }
        if( $category_low_id != $item['category_low_id'] && $category_low_id != null ) {
            $dataset[] = [
                'label' => $category,
                'data' => $datas_lbr,
                'fill' => false,
                'backgroundColor' => sprintf('rgba(%s,%s,%s,1)', rand(1,255), rand(1,255), rand(1,255)),
                'yAxisID' => 'y-axis-'.$i
            ];
        }
        $funcion = function($a, $b){
            return (new \DateTime('01-'.$a))->format('Ymd') - (new \DateTime('01-'.$b))->format('Ymd');
        };

        usort($labels_lbr, $funcion);


        $data_cf = $search->findCostEffectiveness((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $labels_cf = [];
        $rentabilidad_cf = [];
        $datasets_cf = [];
        foreach($data_cf as $item){
            $date = new \DateTime($item['period']."-01");

            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $labels_cf[] = $date->format('m-Y');
                $rentabilidad_cf[] = ceil(($item['diferencia']/ $item['facturado'] ) *100);
            }
        }

        $datasets_cf[] = [
            'label' => ReportsModule::t('app', 'Effectiveness'),
            'data' => $rentabilidad_cf,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];

        $data_udv = $search->findUpsAndDowns((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $labels_udv = [];
        $datasets_udv = [];
        $altas_udv = [];
        $bajas_udv = [];
        foreach($data_udv as $item){
            $date = new \DateTime($item['period']."01");

            if($date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $labels_udv[] = $date->format('m-Y');
                $altas_udv[] = $item['up'];
                $bajas_udv[] = $item['down'];
            }
        }

        $datasets_udv[] = [
            'label' => ReportsModule::t('app', 'Up'),
            'data' => $altas_udv,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];
        $datasets_udv[] = [
            'label' => ReportsModule::t('app', 'Down'),
            'data' => $bajas_udv,
            'fill' => false,
            'backgroundColor' => 'rgba(0,255,0,1)',
        ];



        return $this->render('/reports/dashboard', [
            'model' => $search,
            'cols_company_passive'  => $cols_company_passive,
            'data_company_passive'  => $data_company_passive,

            'cols_cvpm'  => $cols_cvpm,
            'data_cvpm'  => $data_cvpm,
            'colors_cvpm' => $colors_cvpm,

            'cols_cp' => $cols_cp,
            'datas_cp' => $datas_cp,

            'data1'  => $process($data1),
            'data2'  => $process($data2),
            'data3'  => $process($data3),
            'data4'  => $process($data4),

            'cols_lbm' => $cols_lbm,
            'data_lbm' => $datas_lbm,

            'labels_lbr'        => $labels_lbr,
            'dataset_lbr'       => $dataset_lbr,

            'labels_cf' => $labels_cf,
            'datasets_cf' => $datasets_cf,

            'labels_udv' => $labels_udv,
            'datasets_udv' => $datasets_udv

        ]);
    }

    /**
     * @return string
     * Devuelve una vista con la catidad de clientes activos, el procentaje de clientes que tienen intalada la app, y el porcentaje de clientes que hacen uso activo de la app
     */
    public function actionMobileApp()
    {
        $config_min_last_update = Config::getValue('month-qty-to-declare-app-uninstalled');
        Yii::$app->session->setFlash('info', "Se considera como desinstalada la app cuando el último uso registrado de la app es mayor a $config_min_last_update meses. <br> La cantidad de meses que se tiene en cuenta para este cálculo puede ser configurable desde Home->Aplicación->Mobile app->Meses para declarar la app desinstalada");
        $search = new UserAppActivitySearch();
        $statistics = $search->searchStatistics(Yii::$app->request->get());

        $reportSearch = new ReportSearch();
        $paymentsStatistics = $reportSearch->notifyPaymentStatistics();
        $paymentExtensionStatistics = $reportSearch->paymentExtensionStatistics();

        return $this->render('/reports/mobile-app', [
            'searchModel' => $search,
            'statistics' => $statistics,
            'paymentsStatistics' => $paymentsStatistics,
            'paymentExtensionStatistics' => $paymentExtensionStatistics
        ]);
    }

    public function actionCustomersByNode()
    {
        $search = new CustomerSearch();

        $dataProvider = $search->findByNode(Yii::$app->request->getQueryParams());

        return $this->render('customer-by-node', ['dataProvider' => $dataProvider]);
    }
}
