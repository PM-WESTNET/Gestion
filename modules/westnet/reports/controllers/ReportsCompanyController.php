<?php

namespace app\modules\westnet\reports\controllers;

use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use app\modules\sale\models\Company;
use app\modules\westnet\models\Vendor;
use app\modules\westnet\reports\ReportsModule;
use app\modules\westnet\reports\models\ReportData;
use app\modules\westnet\reports\search\ReportSearch;
use app\modules\westnet\reports\search\ReportCompanySearch;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\sale\models\Customer;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\westnet\notifications\models\SiroPaymentIntention;
use app\modules\westnet\notifications\models\search\SiroPaymentIntentionSearch;
use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\checkout\models\PaymentMethod;

class ReportsCompanyController extends Controller
{

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionCustomersPerMonth()
    {
        $search = new ReportCompanySearch();
        $data = $search->findReportDataActiveContracts((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item->period . '01');
            if($item->company_id && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $company = Company::findOne($item->company_id);
                $cols[] = $date->format('m-Y') . ' - ' . $company->name;
                $datas[] = $item->value;
            }
        }

        return $this->render('/reports/customer-per-month-by-company', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas
        ]);
    }


    /**
     * @return string
     * @throws \Exception
     * Variacion de bajas y altas de clientes por empresa
     */
    public function actionCustumerVariationPerMonth()
    {
        $search = new ReportCompanySearch();
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
            if ($item['company_id'] && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $company = Company::findOne($item['company_id']);
                $cols[] = $date->format('m-Y') . ' - ' . $company->name;
                $datas[] = $item['diferencia'];

                $colors[] = (($item['diferencia'] > 0) ? 'green' : 'red');
            }
        }

        return $this->render('/reports/costumer-variation-per-month-by-company', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas,
            'colors' => $colors
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * Muestra un gráfico con un promedio de las facturas adeudadas por empresa
     */
    public function actionDebtBills()
    {
        $search = new ReportCompanySearch();
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
                if ($item['company_id'] && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                    $company = Company::findOne($item['company_id']);
                    $cols[] = $date->format('m-Y') . ' - ' . $company->name ;
                    $datas[] = $item['value'];
                }
            }
            return [
                'cols' => $cols,
                'data' => $datas
            ];
        };

        return $this->render('/reports/debt-bills-by-company', [
            'model' => $search,
            'data1' => $process($data1),
            'data2' => $process($data2),
            'data3' => $process($data3),
            'data4' => $process($data4),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * Muestra un grafico con el porcentaje de bajas mensuales
     */
    public function actionLowByMonth()
    {
        $search = new ReportCompanySearch();
        $data = $search->findLowByMonth((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $cols = [];
        $datas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . '-01');
            if ($item['company_id'] && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $company = Company::findOne($item['company_id']);
                $cols[] = $date->format('m-Y') . ' - ' . $company->name;
                $datas[] = $item['porcentage'];
            }
        }

        return $this->render('/reports/low-by-month-by-company', [
            'model' => $search,
            'cols' => $cols,
            'data' => $datas
        ]);
    }

    /**
     * Rentabilidad
     *
     * @return string
     */
    public function actionCostEffectiveness()
    {
        $search = new ReportCompanySearch();
        $data = $search->findCostEffectiveness(Yii::$app->request->getQueryParams());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $labels = [];
        $rentabilidad = [];
        $datasets = [];
        $earn = 0;
        $outgo = 0;
        $outgoEmployee = 0;
        $account_movements = 0;
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . "-01");
            $earn += $item['facturado'];
            $outgo += $item['pagos'];
            $outgoEmployee += $item['pagos_employee'];
            $account_movements += $item['pagos_account'];

            if ($item['company_id'] && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $company = Company::findOne($item['company_id']);
                $labels[] = $date->format('m-Y') .' - '. $company->name;
                $diff_facturado = $item['facturado'] > 0 ? $item['diferencia'] / $item['facturado'] : $item['diferencia'] / abs($item['diferencia']);
                $rentabilidad[] = round($diff_facturado * 100, 2);
            }
        }

        $datasets[] = [
            'label' => ReportsModule::t('app', 'Effectiveness'),
            'data' => $rentabilidad,
            'fill' => false,
            'backgroundColor' => 'rgba(0,0,255,1)',
        ];

        return $this->render('/reports/cost-effectiveness-by-company', [
            'model' => $search,
            'labels' => $labels,
            'datasets' => $datasets,
            'earn' => $earn,
            'outgo' => $outgo,
            'outgoEmployee' => $outgoEmployee,
            'account_movements' => $account_movements
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * Variacion total de clientes por empresa
     */
    public function actionUpDownVariation()
    {
        $search = new ReportCompanySearch();
        $data = $search->findUpsAndDowns((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        $from = new \DateTime($search->date_from);
        $to = new \DateTime($search->date_to);

        $labels = [];
        $datasets = [];
        $altas = [];
        $bajas = [];
        foreach ($data as $item) {
            $date = new \DateTime($item['period'] . "01");
            if ($item['company_id'] && $date->format('Ym') >= $from->format('Ym') && $date->format('Ym') <= $to->format('Ym')) {
                $company = Company::findOne($item['company_id']);
                $labels[] = $date->format('m-Y'). ' - ' . $company->name;
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

        return $this->render('/reports/up-down-variation-by-company', [
            'model' => $search,
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * Renderiza un gràfico con el porcentaje de egresos e ingresos por empresa
     */
    public function actionInOut()
    {
        $search = new ReportCompanySearch();
        if (!isset($_GET['ReportSearch'])) {
            $search->date_from = (new \DateTime('first day of this month'))->format('01-m-Y');
            $search->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
        }

        if(!$search->company_id) {
            $search->company_id = Company::find()->where(['status' => 'enabled'])->one();
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


        return $this->render('/reports/in-out-by-company', [
            'searchModel' => $search,
            'data'  => $dataModel,
            'movements' => $movementsModel,
            'totalCobrado' => $totalCobrado,
            'totalPagado' => $totalPagado,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * Renderiza un gràfico con el porcentaje de egresos e ingresos por empresa
     */
    public function actionHistory()
    {
        $searchModel = new ContractSearch();
        $searchModel->setScenario('vendor-search');

        $vendor = Vendor::findByUserId(Yii::$app->user->id);
        if (empty($vendor)) {
            throw new HttpException(404, Yii::t('app', 'Are you a vendor?'));
        }
        $searchModel->vendor_id = $vendor->vendor_id;
        
        $search = new ReportCompanySearch();
        if (!isset($_GET['ReportSearch'])) {
            $search->date_from = (new \DateTime('first day of this month'))->format('01-m-Y');
            $search->date_to = (new \DateTime('last day of this month'))->format('d-m-Y');
        }

        if(!$search->company_id) {
            $search->company_id = Company::find()->where(['status' => 'enabled'])->one();
        }

        $query = $search->findInOut(Yii::$app->request->queryParams);
        $dataModel = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);




        return $this->render('/reports/history', [
            'searchModel' => $searchModel,
            'data'  => $dataModel
        ]);
    }


    public function actionPaymentIntention(){
        $searchModel = new SiroPaymentIntentionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/reports/payment-intention', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    public function actionPaymentIntentionView($id){
        $model = SiroPaymentIntention::FindPaymentIntentionByID($id);

        return $this->render('/reports/payment-intention-view',['model' => $model]);
    }

    public function actionResultPaymentIntention($reference,$id_resultado){
        return json_encode(ApiSiro::SearchPaymentIntention($reference, $id_resultado));
    }

    public function actionPaymentIntentionGeneratePay($id){
        $paymentIntention = SiroPaymentIntention::find()->where(['siro_payment_intention_id' => $id])->one();
        $result_search = ApiSiro::SearchPaymentIntention($paymentIntention->reference,$paymentIntention->id_resultado);
        
        $paymentIntention->updatedAt = date('Y-m-d_H-i');
        $paymentIntention->status = ($result_search['PagoExitoso']) ? "payed" : (($result_search['Estado'] == 'CANCELADA')?'canceled':'pending');
        $paymentIntention->id_operacion = $result_search['IdOperacion'];
        $paymentIntention->estado = $result_search['Estado'];
        $paymentIntention->fecha_operacion = $result_search['FechaOperacion'];
        $paymentIntention->fecha_registro = $result_search['FechaRegistro'];
        $paymentIntention->save(false);

        if($result_search['PagoExitoso'] == 'payed'){
            $transaction = Yii::$app->db->beginTransaction();
            $customer = Customer::findOne(['customer_id' => $paymentIntention->customer_id]);
            $payment_method = PaymentMethod::findOne(['name' => 'Botón de Pago']);

            $payment = new Payment([
                'customer_id' => $customer->customer_id,
                'amount' => abs($customer['current_account_balance']),
                'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
                'company_id' => $customer->company_id,
                'date' => (new \DateTime('now'))->format('Y-m-d'),
                'status' => 'closed'
            ]);
                
            if ($payment->save(false)) {
                $paymentIntention->payment_id = $payment['payment_id'];
    
                $payment_item = new PaymentItem();
                $payment_item->amount = $payment['amount'];
                $payment_item->description = 'Intención de Pago (Banco Roela) ' . $paymentIntention['siro_payment_intention_id'];
                $payment_item->payment_method_id = $payment_method->payment_method_id;
                $payment_item->payment_id = $payment->payment_id;
                $payment_item->paycheck_id = null;
                
                $customer->current_account_balance -= $customer->current_account_balance;

                $paymentIntention->save(false);
                $payment_item->save(false);
                $customer->save(false);

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Creación de pago generada correctamente.');
                
            } else {
                Yii::$app->session->setFlash('error', 'La creacion del pago no pudo ser generada.');
                $transaction->rollBack();
            }
        }else if($result_search['Estado'] == 'CANCELADA'){
            Yii::$app->session->setFlash('error', 'La intención de pago se encuentra cancelada.');

        }else{
            Yii::$app->session->setFlash('error', 'Ha habido un error con la intención de pago.');
        }
        $this->redirect(array('payment-intention-view', 'id' => $paymentIntention->siro_payment_intention_id));
    }

    public function actionHowDidYouKnowTheCompany(){
        $reportSearch = new ReportSearch();
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->searchPublicityShape(Yii::$app->request->get(),(!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        
        return $this->render('/reports/how-did-you-know-the-company',['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'reportSearch' => $reportSearch]);
    }

    public function actionHowDidYouKnowTheCompanyViewCustomer($publicity_shape){
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->searchCustomerByPublicityShape(Yii::$app->request->get());

        return $this->render('/reports/how-did-you-know-the-company-view-customer',['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }
}
