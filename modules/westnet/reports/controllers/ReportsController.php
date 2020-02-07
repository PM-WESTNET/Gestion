<?php

namespace app\modules\westnet\reports\controllers;

use app\components\helpers\GraphData;
use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\search\UserAppActivitySearch;
use app\modules\sale\models\Customer;
use app\modules\westnet\models\NotifyPayment;
use app\modules\westnet\models\PaymentExtensionHistory;
use app\modules\westnet\models\search\ConnectionForcedHistorialSearch;
use app\modules\westnet\models\search\NotifyPaymentSearch;
use app\modules\westnet\models\search\PaymentExtensionHistorySearch;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\ReportsModule;
use app\modules\westnet\reports\search\CustomerSearch;
use app\modules\westnet\reports\search\ReportSearch;
use Yii;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * CustomerController
 * Implementa los reportes relacionados con clientes.
 *
 */
class ReportsController extends Controller
{

    const COLORS = [
            'rgba(249, 192, 191)',
            'rgba(254, 229, 206)',
            'rgba(255, 241, 195)',
            'rgba(243, 255, 195)',
            'rgba(199, 255, 192)',
            'rgba(194, 255, 235)',
            'rgba(194, 248, 254)',
            'rgba(194, 230, 255)',
            'rgba(194, 208, 255)',
            'rgba(211, 192, 255)',
            'rgba(232, 195, 255)',
            'rgba(255, 194, 222)',
            'rgba(255, 193, 221)'
        ];

    const BORDER_COLORS = [
            'rgba(241, 134, 132)',
            'rgba(241, 183, 132)',
            'rgba(241, 216, 132)',
            'rgba(220, 241, 132)',
            'rgba(144, 241, 132)',
            'rgba(132, 241, 205)',
            'rgba(132, 231, 241)',
            'rgba(132, 196, 241)',
            'rgba(132, 158, 241)',
            'rgba(165, 132, 241)',
            'rgba(200, 132, 241)',
            'rgba(241, 132, 233)',
            'rgba(241, 132, 182)'
        ];

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

    /**
     * Muestra un reporte de la cantidad de clientes por medio de publicidad.
     */
    public function actionCustomerByPublicityShape()
    {
        $search = new ReportSearch();
        $search->load((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        if(!$search->date_from) {
            $search->date_from = (new \DateTime('now'))->modify('-1 month')->format('Y-m-01');
        }

        if(!$search->date_to) {
            $search->date_from = (new \DateTime('now'))->format('Y-m-01');
        }

        $data = $search->searchCustomerByPublicityShape((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $datas = [];
        $cols = [];
        $colors = [];
        $border_colors = [];

        $asigned_color = [
            'banner' => 'rgba(249, 192, 191)',
            'poster' => 'rgba(254, 229, 206)',
            'web' => 'rgba(255, 241, 195)',
            'other_customer' => 'rgba(243, 255, 195)',
            'facebook' => 'rgba(199, 255, 192)',
            'street_banner' => 'rgba(194, 255, 235)',
            'magazine' => 'rgba(194, 248, 254)',
            'door_to_door' => 'rgba(194, 230, 255)',
            'competition' => 'rgba(194, 208, 255)',
            'brochure' => 'rgba(211, 192, 255)',
            'gigantografía' => 'rgba(232, 195, 255)',
            'pantalla-led' => 'rgba(255, 194, 222)',
            'instagram' => 'rgba(255, 193, 221)'
        ];

        $border_asigned_color = [
            'banner' => 'rgba(241, 134, 132)',
            'poster' => 'rgba(241, 183, 132)',
            'web' => 'rgba(241, 216, 132)',
            'other_customer' => 'rgba(220, 241, 132)',
            'facebook' => 'rgba(144, 241, 132)',
            'street_banner' => 'rgba(132, 241, 205)',
            'magazine' => 'rgba(132, 231, 241)',
            'door_to_door' => 'rgba(132, 196, 241)',
            'competition' => 'rgba(132, 158, 241)',
            'brochure' => 'rgba(165, 132, 241)',
            'gigantografía' => 'rgba(200, 132, 241)',
            'pantalla-led' => 'rgba(241, 132, 233)',
            'instagram' => 'rgba(241, 132, 182)'
        ];

        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . '-01');
            $cols[] = $date->format('m-Y') . ' - '. Yii::t('app', $item['publicity_shape']);
            $datas[] = $item['customer_qty'];
            array_push($colors, $asigned_color[$item['publicity_shape']]);
            array_push($border_colors, $border_asigned_color[$item['publicity_shape']]);
        }


        return $this->render('customer-by-publicity-shape',[
            'model' => $search,
            'cols' => $cols,
            'data' => $datas,
            'colors' => $colors,
            'border_colors' => $border_colors
        ]);
    }

    /**
     * Muestra un reporte de la cantidad de informes de pago en dos gráficos:
     * Torta: muestra la cantidad de informes de pago discriminados por medios de pago y si son de la app o ivr
     * Líneas: muestra la cantidad de informes de pago discriminados por otigen, es decir, si son de la app o ivr
     */
    public function actionNotifyPaymentsGraphics()
    {
        $search = new ReportSearch();
        $search->load((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $datas = [];
        $cols = [];

        $dataslineal = [];
        $colslineal = [];

        if(!$search->date_from) {
            $search->date_from = (new \DateTime('now'))->modify('-1 month')->format('Y-m-01');
        }

        if(!$search->date_to) {
            $search->date_to = (new \DateTime('now'))->format('Y-m-01');
        }

        $data = $search->searchNotifyPayments((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $dataLineal = $search->searchNotifyPaymentsByDate((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $first_date = array_key_exists(0, $dataLineal) ? $dataLineal[0] : $search->date_from ;
        $last_date = end($dataLineal) ? end($dataLineal) : $search->date_to;

        $graph  = new GraphData([
            'fromdate' => $dataLineal[0]['date'],
            'todate' => end($dataLineal)['date'],
        ]);

        $colslineal = $graph->getSteps();


        //Columnas del grafico de torta
        foreach ($data as $item) {
            $cols[] = $item['name'];
            $datas[] = $item['qty'];
        }

        $data_app = [];
        $data_ivr = [];

        $before_app= 0;
        $before_ivr = 0;

        //Completo los array con las fechas que comprenden el período
        foreach ($colslineal as $item) {
            $from_app = false;
            $from_ivr = false;

            foreach ($dataLineal as $datal) {
                if($datal['from'] == NotifyPayment::FROM_APP) {
                    if($datal['date'] == $item) {
                        $before_app += (int)$datal['qty'];
                        array_push($data_app, $before_app);
                        $from_app = true;
                    }
                }

                if($datal['from'] == NotifyPayment::FROM_IVR) {
                    if($datal['date'] == $item) {
                        $before_ivr += (int)$datal['qty'];
                        array_push($data_ivr, $before_ivr);
                        $from_ivr = true;
                    }
                }
            }

            //Si el valor no está ni en la linea de la app o ivr se agrega 0 para esa fecha
            if(!$from_app ) {
                array_push($data_app, $before_app);
                $from_app = false;
            }

            if(!$from_ivr ) {
                array_push($data_ivr, $before_ivr);
                $from_ivr = false;
            }

//            if(!$from_ivr ) {
//                array_push($data_ivr, $counter_ivr);
//                $from_ivr = false;
//            }
        }


        return $this->render('notify-payments',[
            'model' => $search,
            'cols' => $cols,
            'data' => $datas,
            'colors' => self::COLORS,
            'border_colors' => self::BORDER_COLORS,

            'colslineal' => $colslineal,
            'data_app' => $data_app,
            'data_ivr' => $data_ivr
        ]);
    }

    /**
     * Muestra un reporte de la cantidad de informes de pago en dos gráficos:
     * Torta: muestra la cantidad de informes de pago discriminados por medios de pago y si son de la app o ivr
     * Líneas: muestra la cantidad de informes de pago discriminados por otigen, es decir, si son de la app o ivr
     */
    public function actionPaymentExtensionGraphics()
    {
        $searchModel = new ReportSearch();
        $colslineal = [];
        $cols_tart = [];
        $data_tart = [];

        if (!$searchModel->date_from) {
            $searchModel->date_from = (new \DateTime('now'))->modify('-1 month')->format('Y-m-01');
        }

        if (!$searchModel->date_to) {
            $searchModel->date_to = (new \DateTime('now'))->format('Y-m-01');
        }

        $datas = $searchModel->searchPaymentExtensionQty((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $dataFromQty = $searchModel->searchPaymentExtensionQtyFrom((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $graph = new GraphData([
            'fromdate' => (new \DateTime($searchModel->date_from))->format('Y-m-d'),
            'todate' => (new \DateTime($searchModel->date_to))->format('Y-m-d'),
        ]);
        $colslineal = $graph->getSteps();

        $data_app = [];
        $data_ivr = [];

        //Columnas del grafico de torta
        foreach ($dataFromQty as $item) {
            $cols_tart[] = $item['from'];
            $data_tart[] = $item['qty'];
        }

        $counter_app = 0;
        $counter_ivr = 0;
        $before_app = 0;
        $before_ivr = 0;

//        var_dump($colslineal);
//        die();
        //Completo los array con las fechas que comprenden el período
        foreach ($colslineal as $item) {
            $from_app = false;
            $from_ivr = false;


            foreach ($datas as $data) {
                if ($data['from'] == PaymentExtensionHistory::FROM_APP) {
//                    var_dump($data['date']);
//                    var_dump($item);
                    if ($data['date'] == $item) {
                        $before_app += (int)$data['qty'];
                        array_push($data_app, $before_app);
                        $from_app = true;
                    }
                }

                if ($data['from'] == PaymentExtensionHistory::FROM_IVR) {
                    if ($data['date'] == $item) {
                        $before_ivr += (int)$data['qty'];
                        array_push($data_ivr, (int)$data['qty']);
                        $from_ivr = true;
                    }
                }
            }



            //Si el valor no está ni en la linea de la app o ivr se agrega 0 para esa fecha
            if (!$from_app) {
                array_push($data_app, $before_app);
                $from_app = false;
            }

            if (!$from_ivr) {
                array_push($data_ivr, $before_ivr);
                $from_ivr = false;
            }
        }

        return $this->render('payment-extension-graphic', [
            'model' => $searchModel,
            'colslineal' => $colslineal,
            'data_app' => $data_app,
            'data_ivr' => $data_ivr,
            'cols_tart' => $cols_tart,
            'data_tart' => $data_tart,
            'colors' => self::COLORS,
            'border_colors' => self::BORDER_COLORS
        ]);
    }
}
