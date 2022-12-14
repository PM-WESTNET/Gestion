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
use app\modules\sale\modules\contract\components\ContractLowService;
use yii\data\ArrayDataProvider;
 
use Da\QrCode\QrCode;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\helpers\Html;
use yii\helpers\Url;
use DateTime;
use yii\db\Transaction;


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
     * Env??a un mail con un pdf del comprobante
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
    
    public function actionTestActionMora(){
        $result = ContractController::actionMoraV3();
        // $result2 = ContractController::actionMoraV2();

        // check if any ip is going through as 0.0.0.0 to portal captivo
        $testArr = array_column($result,'ip');
        foreach($testArr as $ip){
            if($ip == '0.0.0.0' or $ip == null or $ip == '0' or $ip == 0){
                die('and go to hell');
            }
        }

        die('mora trace end');
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

    public function actionTestQueues($connection_id){
        // $start = microtime(true);
        $conn = Connection::findOne($connection_id);
        // var_dump('query runned found connection TIME',(microtime(true)-$start));
        $conn->node_id = '90'; // comment if needed
        $conn->updateIp(); // comment if you dont want to update the IP (also triggers SecureConnectionUpdate->update() func)
        // var_dump('ip updated TIME',(microtime(true)-$start));
        $savedBool = $conn->save();
        // var_dump('after save TIME',(microtime(true)-$start));
        die('//');
    }

    public function actionTestDeleteContract($contract_id){
        // /index.php?r=test/test-delete-contract&contract_id=935
        $transaction = Yii::$app->db->beginTransaction();

        $model = Contract::findOne($contract_id);
        $model->status = Contract::STATUS_LOW_PROCESS; //testing
        var_dump($model->status);
        // die();
        $model->setScenario('cancel');
        $model->to_date = (new DateTime())->format('d-m-Y');
        if ($model->save(true)) {               
            
            $cti = new ContractToInvoice();
            
            if ($cti->cancelContract($model)) {
                $model->customer->updateAttributes(['status' => Customer::STATUS_DISABLED]);
                var_dump(Yii::t('app', 'Contract canceled successful'));
            }else{
                var_dump('failed to save contract to invoice');
                var_dump(Yii::t('app', 'Can\`t cancel this contract'));
            }
        }else{
            var_dump('failed to save contract');
            var_dump(Yii::t('app', 'Can\`t cancel this contract'));
        }
        
        $transaction->rollback();
        die('work');

        return false;
    }

    public function actionUpdateContractConnection($contract_id){
        // /index.php?r=test/update-contract-connection&contract_id=68975
        $transaction = Yii::$app->db->beginTransaction();
        $model = Contract::findOne($contract_id);
        $connection = Connection::findOne(['contract_id' => $model->contract_id]);
        echo "<pre>";
        // var_dump($model->status);
        var_dump($connection->status);
        var_dump($connection->status_account);
        var_dump($model->customer_id);
        // $connection->status_account = 'enabled';

        if ($connection->validate()) {
                if($connection->save()){


                        echo "\nworked\n";
                }
                else{
                     	echo "\ndidnt work\n";
                }

        }

	    // var_dump($model->status);
        var_dump($connection->status);
        var_dump($connection->status_account);
        var_dump($model->customer_id);
        echo "</pre>";

        $transaction->rollback();
        die('end--');

        return false;
    }

    public function actionTestBill($id){
        $bill = Bill::findOne($id);
        var_dump($bill->bill_id);
        if($bill){
            $taxes = $bill->getTaxesApplied();
            var_dump('$taxes',$taxes);
            var_dump('///');
            $amount = round($bill->calculateTotal(),2);
            var_dump($amount);
        }

        die('end');
        return true;
    }

    public function actionTestLowProcess($id){
        $contract = Contract::findOne($id);
        $credit = 0;
        $category_id = 15;
        $date = '2022-04-29';
        $date = DateTime::createFromFormat('Y-m-d', $date);
        // var_dump($date);die();

        $service = new ContractLowService();
        try{
            if($service->startLowProcess($contract, $date, $category_id, $credit)){
                var_dump('worked');

            }else{
                var_dump('failed');
            }   
        }catch(\Exception $ex){
            var_dump($ex);
            // die('end2');
        }
        // var_dump($contract);
        // die('end');
        return true;
    }

    public function actionGetCustomerDefaultBillType($id){
        $customer = Customer::findOne($id);

        $billType = $customer->getDefaultBillType();
        var_dump($billType);

        // die();
        return true;
    }

    /**
     * renders a view to see all images associated with notification layouts and the app in general.
     * 
     * 
     * this was created to test the standardized version of the image files, for example:
     * changing from westnet-logo-long -> company-logo-long.
     * and adding the specified path to app params probably.
     * todo: errors to solve --
     * no standardized name for image files.
     * multiple layouts use different images and depends on company subjectivity.
     * image file existence is not checked before usage, allowing a module
     * (like mailing) to work even if no image is sent.
     * there is no test environment (this function purpose)
     * 
     */
    public function actionTestCompanyImages(){
        // $connection = Connection::findOne('64');
        // $customer_code = $connection->contract->customer->code;
        // var_dump($customer_code);
        // die();
        // $var->connection->contract->customer->code;
        return $this->render('company-images');
    }

}
