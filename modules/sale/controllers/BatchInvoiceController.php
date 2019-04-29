<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 12/12/15
 * Time: 09:48
 */

namespace app\modules\sale\controllers;


use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use Yii;
use yii\data\ActiveDataProvider;
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

            Yii::$app->session->set( '_invoice_all_', [
                '_invoice_total_' => 0,
                '_invoice_cantidad_', 0
            ]);

            $cti = new ContractToInvoice();
            $cti->invoiceAll(Yii::$app->request->post());

            $messages = $cti->getMessages();

            return [
                'status' => 'success',
                'messages' => $messages
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

        return Yii::$app->session->get($process, [
            'total' => 0,
            'qty'   => 0
        ]);

    }

    /**
     * Lists all Contracts for invoice
     * @return mixed
     */
    public function actionCloseInvoicesIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->seachWithOutElectronic(Yii::$app->request->getQueryParams()),
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

            Yii::$app->session->set( '_invoice_close_', [
                'total' => 0,
                'qty' => 0
            ]);
            $i = 1;
            $searchModel = new BillSearch();
            $query = $searchModel->seachWithOutElectronic(Yii::$app->request->post());
            $total = $query->count();
            $retMessages = [];
            foreach($query->all() as $bill ) {
                $bill->close();

                $messages = Yii::$app->session->getAllFlashes();
                $fn = function($messages) {
                    $rtn = [];
                    foreach ($messages as $message) {
                        $rtn[] = Yii::t('afip', $message);
                    }
                    return $rtn;
                };
                foreach($messages as $key=>$message) {

                    $retMessages[$key][] = ($bill->customer ? $bill->customer->name : '' ) . " - " . Yii::t('app', 'Bill') . ' ' .
                        Yii::t('app', 'Status') . ' '  . Yii::t('app', $bill->status) .  ' - ' . implode('<br/>', $fn($message) ) ;
                }

                Yii::$app->session->set( '_invoice_close_', [
                    'total' => $total,
                    'qty'=> $i
                ]);
                Yii::$app->session->close();
                $i++;
            }

            return [
                'status' => 'success',
                'messages' => $retMessages
            ];
        }
    }

}