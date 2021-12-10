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

    public function actionTestBillClosing(){
        $bill = Bill::findOne(2214082);
        //return var_dump($bill);
        return $bill->close()?"funciona":"no funciona";
    }

}
