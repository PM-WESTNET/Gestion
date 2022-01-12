<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;



use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\mailing\components\sender\MailSender;
use app\modules\mobileapp\v1\models\AppFailedRegister;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\modules\mobileapp\v1\models\UserAppHasCustomer;
use app\modules\mobileapp\v1\models\ValidationCode;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\Product;
use app\modules\sale\models\TaxCondition;
use app\modules\westnet\ecopagos\models\Ecopago;
use app\modules\westnet\models\ConnectionForcedHistorial;
use app\modules\westnet\models\NotifyPayment;
use webvimark\modules\UserManagement\models\User;
use yii\db\Exception;
use yii\validators\EmailValidator;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\sale\modules\contract\models\Contract;
use yii\web\UploadedFile;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\modules\westnet\models\PaymentExtensionHistory;
use app\modules\westnet\notifications\components\transports\EmailTransport;
use app\modules\westnet\api\controllers\ContractController;
use app\modules\westnet\models\Connection;
use yii\data\ArrayDataProvider;

use Da\QrCode\QrCode;
use yii\helpers\Html;
use yii\helpers\Url;


class TestController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        $request = \Yii::$app->request;

        if (strpos($request->serverName, 'localhost'))
            return $this->redirect(['site']);

        return $this->render('index');
    }

    public function actionAllButtons() {
        return $this->render('all-buttons');
    }


    /**
     * @param $pdf_key
     * @param $email
     * @return array
     * @throws \Exception
     * Envía un mail con un pdf del comprobante
     */
    public function actionSendBillEmail($pdf_key, $email){

        $id= $pdf_key;//$this->encrypt_decrypt($pdf_key, 'decrypt');

        $bill = Bill::findOne($id);

        if (empty($bill)){
            Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'error',
                'error' => Yii::t('app','Bill not found'),
            ];
        }

        $pointOfSale = $bill->getPointOfSale()->number;

        $pdf = $this->MakePdf($id);

        $pdf = substr($pdf, strrpos($pdf, '%PDF-'));
        $fileName = "/tmp/".'Comprobante'.sprintf("%04d", $pointOfSale) . "-" . sprintf("%08d", $bill->number )."-".$bill->customer_id.".pdf";
        $file = fopen($fileName, "w+");
        fwrite($file, $pdf);
        fclose($file);

        $name = "COMPROBANTE";
        /** @var MailSender $sender */
        $sender = MailSender::getInstance($name, Company::class, $bill->customer->parent_company_id);
        try{
            $sender->send( $email, "Envio de comprobante de: " . $bill->customer->parentCompany->name, [
                'params'=>[
                    'image'         => Html::img(Url::base(true).'/images/logo-westnet.png'),
                    'comprobante'   =>$bill->billType->name . " " . sprintf("%04d", $pointOfSale) . "-" . sprintf("%08d", $bill->number )
                ]],[], [],[$fileName]);
                Yii::$app->response->format= Response::FORMAT_JSON;
                Yii::$app->response->headers->set('Content-type: application/json');
                return [
                    'status' => 'success',
                    'message' => Yii::t('app','Bill has been sended to {email}', ['email' => $email])
                ];
        }catch(Exception $ex){
            Yii::info($ex->getMessage());
        }
        Yii::$app->response->format= Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode(400);
        return [
          'status' => 'error',
          'error' => Yii::t('app','Cant send the bill')
        ];
    }


    /**
     * Prints the pdf of a single Bill.
     * @param integer $id
     * @return mixed
     */
    public function actionPdf($id)
    {

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

        $model = Bill::findOne($id);
        $this->layout = '//pdf';

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $view = $this->render('@app/modules/sale/views/bill/pdf', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);

        return \app\components\helpers\PDFService::makePdf($view);
    }

    public function MakePdf($id){
        $model = Bill::findOne($id);
        $companyData = $model->company;

        $this->layout = '//pdf';

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $jsonCode = [
           "ver" => 1,
           "fecha" => $model->date,
           "cuit" => str_replace("-","",$companyData->tax_identification),
           "ptoVta" => $model->getPointOfSale()->number,
           "tipoCmp" => $model->billType->code,
           "nroCmp" => $model->number,
           "importe" => $model->total,
           "moneda" => "PES",
           "ctz" => 1,
           "tipoDocRec" => $model->customer->documentType->code,
           "nroDocRec" => str_replace("-","",$model->customer->document_number),
           "tipoCodAut" => "E",
           "codAut" => $model->ein
        ];
        $qrCode = (new QrCode("https://www.afip.gob.ar/fe/qr/?p=".base64_encode(json_encode($jsonCode))))
        ->setSize(500)
        ->setMargin(5);

        $view = $this->render('@app/modules/sale/views/bill/pdf', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'qrCode' => $qrCode

        ]);

        $pdf = ' ';

        try{
            $pdf = \app\components\helpers\PDFService::makePdf($view);
        } catch (\Exception $ex){
            \Yii::trace($ex);
        }

        return $pdf;
    }

    public function actionTestEmailTransport($customer_id){
        if(EmailTransport::createLatestBillPDF($customer_id)==[])var_dump("works");
        else var_dump("failed");
    }
    
    public function actionTestActionMoraV2(){
        $result = ContractController::actionMoraV3();
        //$result = ContractController::actionGetBrowserNotificationCustomers();
        
        var_dump($result);die();
        return false;
    }

    public function actionTestGetAllCustomersByState(){
       
        $statuses = array(
            array('enabled','disabled','blocked'), // customer
            array('draft','active','inactive','canceled','low-process','low','no-want','negative-survey'), // contract
            array('enabled','disabled','forced','low'), // connection:status
            array('enabled','disabled','forced','defaulter','clipped','low'), //connection:status_account
        );

        //var_dump("EstadoCliente-EstadoContrato-EstadoConexion-EstadoCuentaConexion:");

        $result = array();
        foreach ($statuses[0] as $customerStatus) {

            foreach ($statuses[1] as $contractStatus) {

                foreach ($statuses[2] as $connectionStatus) {

                    foreach ($statuses[3] as $connectionStatusAccount) {
                        $combination = $customerStatus.'-'.$contractStatus.'-'.$connectionStatus.'-'.$connectionStatusAccount;

                        $customers = Yii::$app->db->createCommand('select * from customer cus
                                                left join contract cont on cont.customer_id = cus.customer_id
                                                left join connection conn on conn.contract_id = cont.contract_id
                                                where cus.status = :customer_status
                                                and cont.status = :contract_status
                                                and conn.status = :connection_status
                                                and conn.status_account = :connection_status_account
                                                group by cus.customer_id')
                                    ->bindValue('customer_status', $customerStatus)
                                    ->bindValue('contract_status', $contractStatus)
                                    ->bindValue('connection_status', $connectionStatus)
                                    ->bindValue('connection_status_account', $connectionStatusAccount)
                                    ->queryAll();
                        $quantity = count($customers);
                        array_push($result, ['combination' => $combination, 'quantity' => $quantity]);
                    }
                }
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => false,
            'sort' => [
                'attributes' => ['combination', 'quantity'],
            ],
        ]);


        return $this->renderPartial('@app/views/test/all-states-export-excel', [
            'dataProvider' => $provider,
        ]);
    }

    public function actionPotentialCustomers(){
        $customer_logs = Yii::$app->db->createCommand("SELECT (
                                                        SELECT cl.customer_log_id FROM customer_log cl
                                                        WHERE cl.customer_id = cus_log.customer_id AND 
                                                        cl.action = 'Actualizacion de Datos de Conexion: Ip4 1'
                                                        ORDER BY cl.customer_log_id DESC
                                                        LIMIT 1
                                                        ) as customer_log_id
                                                    FROM customer_log cus_log WHERE customer_log_id IS NOT NULL 
                                                    GROUP BY cus_log.customer_id")
                                                    ->queryAll();
        foreach ($customer_logs as $log_id) {
            if(!empty($log_id)){
                $customer_log = Yii::$app->db->createCommand("select cl.customer_id, cl.new_value 
                                                from customer_log cl
                                                where cl.customer_log_id = :customer_log_id")
                                                ->bindValue('customer_log_id',$log_id['customer_log_id'])
                                                ->queryOne();

                $contract = Yii::$app->db->createCommand("select cont.contract_id from contract cont
                                                where cont.customer_id = :customer_id
                                                order by cont.contract_id desc
                                                limit 1")
                                                ->bindValue('customer_id',$customer_log['customer_id'])
                                                ->queryOne();
                
                if(!empty($contract)){
                    $connection = Yii::$app->db->createCommand("select conn.connection_id from connection conn
                                                where conn.contract_id = :contract_id")
                                                ->bindValue('contract_id',$contract['contract_id'])
                                                ->queryOne();

                    $ipRepeats = Yii::$app->db->createCommand("select conn.connection_id from connection conn
                                                where conn.connection_id != :connection_id
                                                and conn.ip4_1 = :ip4_1_old")
                                                ->bindValue('connection_id',$connection['connection_id'])
                                                ->bindValue('ip4_1_old',ip2long($customer_log['new_value']))
                                                ->queryAll();

                    if(empty($ipRepeats)){
                        Yii::$app->db->createCommand("UPDATE connection SET ip4_1_old = :ip4_1_old WHERE connection_id = :connection_id")
                                ->bindValue('ip4_1_old', ip2long($customer_log['new_value']))
                                ->bindValue('connection_id',$connection['connection_id'])
                                ->execute();
                    }else{
                        Yii::$app->db->createCommand("UPDATE connection SET ip4_1_old = :ip4_1_old WHERE connection_id = :connection_id")
                                ->bindValue('ip4_1_old', null)
                                ->bindValue('connection_id',$connection['connection_id'])
                                ->execute();
                    }
                    
                    
                }
                
            }

        }
                                    
        return false;
    }

}
