<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 12/12/15
 * Time: 09:48
 */

namespace app\modules\sale\controllers;


use app\modules\config\models\Config;
use app\modules\sale\components\BillExpert;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\InvoiceProcess;
use app\modules\sale\models\PointOfSale;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\TaxCondition;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Json;
use app\components\web\Controller;
use yii\widgets\ActiveForm;

class BatchInvoiceController  extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Contracts for invoice
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContractSearch();
        $searchModel->setScenario('for-invoice');
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->searchForInvoice(Yii::$app->request->getQueryParams()),
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Contracts for invoice with extra filters.
     * @return mixed
     */
    public function actionIndexWithFilters()
    {
        $searchModel = new ContractSearch();
        $searchModel->setScenario('for-invoice');
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->searchForInvoice(Yii::$app->request->getQueryParams()),
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        return $this->render('index-with-filters', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Retorna los tipos de comprobantes de la compañia seleccionada
     * @return array
     */
    public function actionBillType()
    {
        // Yii::$app->layout = '/empty';
        Yii::$app->response->format = 'json';
        $company_id = Yii::$app->request->post('company_id');
        if($company_id) {
            $company = Company::findOne(['company_id'=> $company_id]);
            $out = $company->getBillTypes()->select(['bill_type_id as value', 'name as text'])->where(['<>','multiplier', '0'])
                ->asArray()->all();
            return ['output'=>$out, 'selected'=>''];
        }
        return ['output'=>'', 'selected'=>''];
    }

    /**
     * Genero las facturas en batch.
     */
    public function actionInvoice()
    {
        set_time_limit(0);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            Yii::$app->cache->set( '_invoice_all_', [
                'total' => 0,
                'qty' => 0,
            ]);

            $company_id = Yii::$app->request->post('ContractSearch')['company_id'];
            $bill_type_id = Yii::$app->request->post('ContractSearch')['bill_type_id'];
            $period = Yii::$app->request->post('ContractSearch')['period'];
            $observation = array_key_exists('bill_observation', Yii::$app->request->post()) ? Yii::$app->request->post('bill_observation') : '';

            if(!InvoiceProcess::createInvoiceProcess($company_id, $bill_type_id, $period, $observation, InvoiceProcess::TYPE_CREATE_BILLS, null, null)) {
                return [
                    'status' => 'error',
                    'message' => Yii::t('app', 'Invoice process cannot be started, reload the page to see the real state of the process')
                ];
            };

            return [
                'status' => 'success',
                'message' => Yii::t('app', 'Invoice process has been started, please wait some minutes...')
            ];
        }
    }

    /**
     * Retorna el estado del proceso actual.
     * @return array
     */
    public function actionGetProcess()
    {
        Yii::$app->response->format = 'json';

        $process = Yii::$app->request->post('process');

        $a = Yii::$app->cache->get($process);

        $creation_errors = Yii::$app->cache->get('_invoice_create_errors') ? [Yii::$app->cache->get('_invoice_create_errors')] : [];
        $close_errors = Yii::$app->cache->get('_invoice_close_errors') ? [Yii::$app->cache->get('_invoice_close_errors')] : [];

        $errors = [
            'errors' => array_merge($creation_errors, $close_errors)
        ];

        if (empty($a)) {
            $a = [
                'total' => 0,
                'qty' => 0
            ];
        }

        return array_merge($a, $errors);
    }

    /**
     * Lists all Contracts for invoice
     * @return mixed
     */
    public function actionCloseInvoicesIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->searchPendingToClose(Yii::$app->request->getQueryParams()),
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        return $this->render('close-invoices', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCloseInvoices()
    {
        set_time_limit(0);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            Yii::$app->cache->set( '_invoice_close_', [
                'total' => 0,
                'qty' => 0
            ]);

            $company_id = Yii::$app->request->post('BillSearch')['company_id'];
            $bill_type_id = Yii::$app->request->post('BillSearch')['bill_type_id'];
            $fromDate = Yii::$app->request->post('BillSearch')['fromDate'];
            $toDate = Yii::$app->request->post('BillSearch')['toDate'];


            if(!InvoiceProcess::createInvoiceProcess($company_id, $bill_type_id, null, '', InvoiceProcess::TYPE_CLOSE_BILLS, $fromDate, $toDate)) {
                return [
                    'status' => 'error',
                    'message' => Yii::t('app', 'Invoice process cannot be started, reload the page to see the real state of the process')

                ];
            }

            return [
                'status' => 'success',
                'message' => Yii::t('app', 'Invoice process has been started, please wait some minutes...')
            ];
        }
    }

    /**
     * @param $company_id
     * @param $date
     * @param null $limit
     * @return bool
     * @throws \yii\base\InvalidConfigException
     *
     */
    public function actionFixDoubleBills($company_id, $date, $limit = null, $offset = null)
    {
        set_time_limit(0);
        $bills_not_closed = '';
        $bills_closed = '';
        $taxIvaInscr = TaxCondition::findOne(['name' => 'IVA Inscripto']);
        $bill_type_nota_credito_a = BillType::findOne(['name' => 'Nota Crédito A']);
        $bill_type_nota_credito_b = BillType::findOne(['name' => 'Nota Crédito B']);

        $customersDuplicatedBillsQuery= (new Query())
            ->select(['customer_id'])
            ->from('bill')
            ->andWhere(['date' => \Yii::$app->formatter->asDate($date, 'yyyy-MM-dd')])
            ->andWhere(['status' => 'closed'])
            ->andWhere(['<>','total', 0])
            ->groupBy(['customer_id'])
            ->having(['>', 'count(*)', 1])
            ->limit($limit)
            ->offset($offset);

        $customersDuplicatedBills = $customersDuplicatedBillsQuery->all();
        $customers_id = array_map(function($customer){ return $customer['customer_id'];}, $customersDuplicatedBills);

        $customers = Customer::find()
            ->andWhere(['IN', 'customer_id', $customers_id])
            ->andWhere(['company_id' => $company_id])
            ->all();

        $point_of_sale = PointOfSale::findOne(['company_id' => $company_id, 'default' => 1]);

        if (empty($point_of_sale)) {
            return false;
        }

        foreach ($customers as $customer) {

            if ($customer->tax_condition_id === $taxIvaInscr->tax_condition_id) {
                $bill_type = $bill_type_nota_credito_a;
            }else {
                $bill_type = $bill_type_nota_credito_b;
            }

            $bills = $customer->getBills()->orderBy(['bill.timestamp' => SORT_DESC])->all();

            if (!empty($bills) && $bills[0]->total === $bills[1]->total && $bills[0]->bill_type_id == $bills[1]->bill_type_id) {
                $bill = $this->createBill($bill_type->bill_type_id, $point_of_sale->point_of_sale_id,$customer, $bills[0]->amount, $bills[0]->total);
                if($bill->close()){
                    $bills_closed .= $bill->bill_id .', ';
                } else {
                    $bills_not_closed .= $bill->bill_id .', ';
                };
                sleep(1);
            }
        }

        if($bills_not_closed) {
            Yii::$app->session->setFlash('error', 'Cant close bills:' .$bills_not_closed);
        }

        if($bills_closed) {
            Yii::$app->session->setFlash('success', 'Closed bills:' .$bills_closed);
        }

        if(empty($bills_closed) && empty($bills_not_closed)) {
            Yii::$app->session->setFlash('info', 'No hay comprobantes duplicados para generar notas de credito');
        }

        return $this->redirect(['/sale/bill']);
    }

    public function createBill($bill_type_id, $point_of_sale_id, $customer, $net, $total)
    {
        $default_unit_id = Config::getValue('default_unit_id');
        $bill = BillExpert::createBill($bill_type_id);
        $bill->company_id = $customer->company->company_id;
        $bill->point_of_sale_id = $point_of_sale_id;
        $bill->customer_id = $customer->customer_id;
        $bill->status = 'draft';

        $bill->save(false);

        $a = $bill->addDetail([
            'product_id' => null,
            'unit_id' => $default_unit_id,
            'qty' => 1,
            'type' => null,
            'unit_net_price' => abs($net),
            'unit_final_price' => abs($total),
            'concept' => 'Corrección error de Facturación',
            'discount_id' => null,
            'unit_net_discount' => null,
        ]);

        $bill->fillNumber();

        return $bill;
    }

    /**
     * Indica si el proceso de facturación se ha iniciado
     */
    public function actionInvoiceProcessCreateBillIsStarted()
    {
        Yii::$app->response->format = 'json';

        if(InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CREATE_BILLS)) {
            return [ 'invoice_process_started' => true ];
        }

        return [ 'invoice_process_started' => false ];
    }

    /**
     * Indica si el proceso de facturación se ha iniciado
     */
    public function actionInvoiceProcessCloseBillIsStarted()
    {
        Yii::$app->response->format = 'json';

        if(InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS)) {
            return [ 'invoice_process_started' => true ];
        }

        return [ 'invoice_process_started' => false ];
    }

}