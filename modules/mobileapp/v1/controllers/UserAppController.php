<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 07/05/18
 * Time: 10:28
 */

namespace app\modules\mobileapp\v1\controllers;


use app\modules\config\models\Config;
use app\modules\mailing\components\sender\MailSender;
use app\modules\mobileapp\v1\components\Controller;
use app\modules\mobileapp\v1\models\AppFailedRegister;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\modules\mobileapp\v1\models\UserAppHasCustomer;
use app\modules\mobileapp\v1\models\ValidationCode;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use app\modules\westnet\ecopagos\models\Ecopago;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\db\Exception;
use yii\validators\EmailValidator;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\sale\modules\contract\models\Contract;

class UserAppController extends Controller
{
    public $modelClass= 'app\modules\mobileapp\v1\models\UserApp';
    protected $exclude_actions= [
        'register',
        'send-validation-code',
        'validate-code',
        'verify-customer',
        'verify-data',
        'set-document-number',
        'create-app-failed-register',
        'customer-data',
        'get-contact-info',
    ];

    public function actions()
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update'], $actions['view']);

        return $actions;
    }

    public function verbs()
    {
        return [
            'register' => ['GET','POST'],
            'send-validation-code' => ['POST'],
            'validate-code' => ['POST'],
            'account' => ['POST'],
            'view' => ['GET', 'HEAD'],
            'update' => ['POST'],
            'bill-pdf' => ['POST', 'GET'],
            'ecopagos' => ['GET'],
            'verify-customer' => ['POST'],
            'verify-data' => ['POST'],
            'set-document-number' => ['POST'],
            'create-app-failed-register' => ['POST']
        ]; // TODO: Change the autogenerated stub
    }

    /**
     * Inicia el login para clientes que tenga documento valido. Si el cliente ya esta registrado en la app
     * no lo vuelve a crear.
     *
     * Debe recibir el nro de documento(document_number) y el codigo de cliente (customer_code)
     *
     * Devuelve  el user_app y los destinatarios para mandar el codigo de validacion
     * @return array
     */
    public function actionRegister(){
        $data= \Yii::$app->request->getBodyParams();

        //Limpio el numero de documento en caso de que venga con guiones ,espacios  o barras.
        $document_number = Customer::clearDocumentNumber($data['document_number']);

        // Busco si ya existe el usuario de la app
        $model= UserApp::findOne(['document_number' => $document_number, 'status' => 'active']);
        $company = Company::findOne(['name' => 'Westnet']);

        //Busco un cliente que coincida con el correo y codigo recibidos y verifico que este activo
        $customer= Customer::find()
            ->andWhere("TRIM(REPLACE(REPLACE(document_number, '/',''), '-', '')) = '$document_number'")
            ->andWhere(['code' => $data['customer_code']])
            ->andWhere(['status' => 'enabled'])
            ->one();

        //Si no existe el usuario de la app, creo uno nuevo
        if (empty($model)){

            $model = new UserApp();

            if (empty($customer)){
                //Si no encuentro un customer activo con la combinacion de documento y code devuelvo error
                \Yii::$app->response->setStatusCode(400);
                return [
                    'status' => 'error',
                    'errors' => [
                        Yii::t('app','Document number and customer code not correspond to an active customer')
                    ]
                ];
            } else {
                if($customer->company_id != $company->company_id && $customer->parent_company_id != $company->company_id){
                    //Si encuentro el customer activo, pero no pertenece a Westnet, devuelvo error
                    \Yii::$app->response->setStatusCode(400);
                    return [
                        'status' => 'error',
                        'errors' => [
                            Yii::t('app','Document number and customer code not correspond to a customer')
                        ]
                    ];
                }
            }

            if(!empty($customer) && $this->createNewUserApp($customer, $data, $model)){
                $destinataries= $customer->getDestinataries();
                return [
                    'status' => 'success',
                    'user' => $model,
                    'destinataries' => $destinataries,

                ];
            }

        }else{
            if (!empty($customer)){

                //Valida que el customer sea de Westnet.
                if($customer->company_id != $company->company_id && $customer->parent_company_id != $company->company_id){
                    //Si encuentro el customer activo, pero no pertenece a Westnet, devuelvo error
                    \Yii::$app->response->setStatusCode(400);
                    return [
                        'status' => 'error',
                        'errors' => [
                            Yii::t('app','Document number and customer code not correspond to a customer')
                        ]
                    ];
                }
                $model->updateAttributes(['player_id' => isset($data['player_id']) ? $data['player_id'] : null]);
                $destinataries= $customer->getDestinataries();
                return [
                    'status' => 'success',
                    'user' => $model,
                    'destinataries' => $destinataries,
                ];
            } else {
                \Yii::$app->response->setStatusCode(400);
                return  [
                    'status' => 'error',
                    'errors' => Yii::t('app','Document number and customer code not correspond to a customer'),
                ];
            }
        }
        \Yii::$app->response->setStatusCode(400);
        return  [
            'status' => 'error',
            'errors' => $model->getErrors(),
        ];
    }

    /**
     * Crea un registro de UserApp formateando el campo document_number
     * dependiendo de la condicion frente  a la AFIP del customer
     */
    public function createNewUserApp($customer, $data, UserApp $model){
        $tax_condition = TaxCondition::findOne(['name' => 'Consumidor Final']);
        $document_type_cuit = DocumentType::findOne(['name' => 'CUIT']);

        //Me aseguro que no sea Consumidor final y su tipo de documento sea CUIT
        if($customer->tax_condition_id != $tax_condition->tax_condition_id && $document_type_cuit->document_type_id == $customer->document_type_id){
            $cuit = Customer::clearDocumentNumber($data['document_number']);
            $cuit = substr($cuit, -11, 2) . '-' . substr($cuit, -9, 8) . '-' .substr($cuit, -1, 2);
            $data['document_number'] = $cuit;
        }

        if ($customer && $model->load($data, '') && $model->save()){
            UserAppActivity::createInstallationRegister($model->user_app_id);
            return true;
        }

        return false;
    }

    /**
     *
     * Envia el codigo de validacion al destinatario que selecciono el cliente en la app.
     *
     * Debe recibir el codigo de cliente (customer_code), user_app_id (lo devuelve register), destinatario (destinatary)
     *
     * Devuelve success o error dependiendo si pudo o no mandar el codigo
     *
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSendValidationCode(){
        $data= \Yii::$app->request->getBodyParams();

        if (empty($data['destinatary']) || empty($data['user_app_id']) ||empty($data['customer_code'])){
            throw new BadRequestHttpException('customer_code user_app_id and destinatary are required');
        }

        $model= UserApp::findOne($data['user_app_id']);

        if (!empty($model)){
            $userAppHasCustomer= UserAppHasCustomer::findOne(['user_app_id' => $model->user_app_id, 'customer_code' => $data['customer_code']]);

            $isEmail = false;

            if(filter_var($data['destinatary'], FILTER_VALIDATE_EMAIL) !== false){
                if(!$model->email){
                    $model->updateAttributes(['email' => $data['destinatary']]);
                }
                $isEmail = true;
            }

            if (empty($userAppHasCustomer)){
                $userAppHasCustomer= new UserAppHasCustomer(['user_app_id' => $model->user_app_id, 'customer_code' => $data['customer_code']]);
                $userAppHasCustomer->save();
            }

            $validationCode= new ValidationCode(['user_app_has_customer_id' => $userAppHasCustomer->user_app_has_customer_id]);
            $validationCode->save();
            if($isEmail) {
                if ($validationCode->sendEmail()){
                    return [
                        'status' => 'success',
                        'message' => Yii::t('app','Validation code has been sended {email}', ['email' => $model->email]),
                    ];
                }
            } else {
                if ($validationCode->sendCodeSms($data['destinatary'])) {
                    return [
                        'status' => 'success',
                        'message' => Yii::t('app', 'Validation code has been sended to {phone}', ['phone' => $data['destinatary']]),
                    ];

                }
            }
        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'status' => 'error',
            'message' => 'Cant send the validation code',
        ];
    }

    /**
     *
     * Valida al cliente con el codigo de validacion recibido, si es valido, deja activo el user_app, termina de relacionar
     * user_app con el customer, y genera el auth token para consultar la api
     *
     * Debe recibir el codigo de cliente (customer_code) y el codigo de validacion enviado (code)
     *
     * Devuelve el user_app y el auth token
     *
     * @return array
     */
    public function actionValidateCode(){
        $data= \Yii::$app->request->getBodyParams();

        if(!isset($data['code']) || !isset($data['customer_code'])){
            \Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'error',
                'error' =>  Yii::t('app','Code and Customer Code is required.'),
            ];
        }

        $validationCode= ValidationCode::find()
            ->andWhere(['code' => $data['code']])
            ->andWhere(['>=', 'expire_timestamp', time()])
            ->orderBy(['validation_code_id' => SORT_DESC])
            ->one();

        if ($validationCode && $validationCode->userAppHasCustomer->customer_code == $data['customer_code']){
            $customer= Customer::findOne(['code' => $data['customer_code']]);

            if ($customer){
                $validationCode->userAppHasCustomer->customer_id= $customer->customer_id;
                $validationCode->userAppHasCustomer->userApp->status= 'active';
                $validationCode->userAppHasCustomer->updateAttributes(['customer_id']);
                $validationCode->userAppHasCustomer->userApp->updateAttributes(['status']);

                return [
                    'status' => 'success',
                    //'customer' => $customer,
                    'userApp' => $validationCode->userAppHasCustomer->userApp,
                    'token' => $validationCode->userAppHasCustomer->userApp->getAuthToken()
                ];
            }
            \Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'error',
                'error' =>  Yii::t('app','Customer Not Found'),
            ];
        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'status' => 'error',
            'error' => Yii::t('app','Cant validate code'),
        ];
    }

    /**
     *
     * Devuelve los datos del user_app que consulta. La busqueda del user la realiza
     * en base al auth token recibido
     *
     * @return UserApp
     */
    public function actionView(){
        $model= $this->getUserApp();
        UserAppActivity::updateLastActivity($model->user_app_id);

        return $model;
    }

    /**
     *
     * Devuelve la lista de ecopagos si el cliente posee una company que tenga habilitada los ecopagos, sino devuelve
     * un array vacio
     *
     * @return array
     */
    public function actionEcopagos(){
        $userApp = $this->getUserApp();
        $ecopagos = Ecopago::find()->all();
        $all_ecopagos = [];
        $related_ecopagos = [];
        $customer_ids = UserAppHasCustomer::find()->select('customer_id')->where(['user_app_id' => $userApp->user_app_id])->all();

        /*if($userApp->getCustomers()->andWhere(['customer.company_id' => Config::getValue('ecopagos_company_id')])->exists()){
            $contracts = Contract::find()->where(['in', 'customer_id', $customer_ids])->all();

            //Relleno related ecopagos
            foreach ($contracts as $contract) {
                if($contract->connection) {
                    $node = $contract->connection->node;
                    if($node){
                        foreach($node->ecopagos as $ecopago){
                           $related_ecopagos[] = $ecopago->description;
                        }
                    }
                }
            }

            //Relleno all ecopagos
            foreach ($ecopagos as $ecopago){
                $all_ecopagos[]= $ecopago->description;
            }
        }*/

        return  [
            'all-ecopagos' => $all_ecopagos,
            'related_ecopagos' => $related_ecopagos
        ];
    }

    /**
     * Retorna un pdf con la factura.
     *
     * @param $id
     * @return int|mixed|\yii\console\Response
     */
    public function actionBillPdf($pdf_key=null)
    {

        $post = Yii::$app->request->getBodyParams();
        $id = ($pdf_key!=null ? $pdf_key : (isset($post['pdf_key']) ?  $post['pdf_key'] : null));
        $bill_id = $this->encrypt_decrypt($id, 'decrypt');

        $response = Yii::$app->runAction('/mobileapp/v1/user-app/pdf', ['id'=> $bill_id]);

        return substr($response, strrpos($response, '%PDF-'));
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




    /**
     * Encripta y desencripta el id.
     *
     * @param $id
     * @param string $action
     * @return bool|string
     */
    private function encrypt_decrypt($id, $action='encrypt')
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = '$_dan4VckasdfF=30923·';
        $secret_iv = '=·"%)"·$5 %GDFgsdfgsdf';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($id, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($id), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }


    /**
     * Agrega un nuevo cliente al user_app, al igual que register devuelve los destinatarios del cliente que se quiere
     * enlazar para poderle enviar el codigo de validacion
     * @return array
     *
     */
    public function actionAddCustomer(){
        $data= \Yii::$app->request->getBodyParams();

        $userApp= $this->getUserApp();

        if (!empty($userApp)){
            $customer= Customer::findOne(['code' => $data['code'], 'status' => 'enabled']);
            $company = Company::findOne(['name' => 'Westnet']);

            if(!empty($customer)) {
                $have_same_document_number = $userApp->getCustomers()->where(['document_number' => $customer->document_number])->exists();
                if ($have_same_document_number && ($customer->company_id == $company->company_id || $customer->parent_company_id == $company->company_id)){
                    if($userApp->addCustomer($customer, true)){
                        $destinataries= $customer->getDestinataries();
                        return [
                            'status' => 'success',
                            'user' => $userApp,
                            'destinataries' => $destinataries,
                        ];
                    }
                     return [
                         'status' => 'error',
                         'errors' => Yii::t('app', 'Error saving the relation')
                     ];
                }

                Yii::$app->response->setStatusCode(400);

                return [
                    'status' => 'error',
                    'errors' => Yii::t('app','The actual account document number and the account that you want to link are not the same')
                ];
            }
        }

        Yii::$app->response->setStatusCode(400);

        return [
            'status' => 'error',
            'errors' => Yii::t('app','Customer not found')
        ];

    }

    /**
     * Similar al send-validation-code con la diferencia de que el user_app ya debe estar logueado en la app
     * @return array
     * @throws BadRequestHttpException
     *
     */
    public function actionValidateCustomer(){
        $data= \Yii::$app->request->getBodyParams();

        if (empty($data['destinatary']) ||empty($data['customer_code'])){
            throw new BadRequestHttpException('customer_code and destinatary are required');
        }

        $model= $this->getUserApp();

        if (!empty($model)){
            $userAppHasCustomer= UserAppHasCustomer::findOne(['user_app_id' => $model->user_app_id, 'customer_code' => $data['customer_code']]);

            if (empty($userAppHasCustomer)){
                $userAppHasCustomer= new UserAppHasCustomer(['user_app_id' => $model->user_app_id, 'customer_code' => $data['customer_code']]);
                $userAppHasCustomer->save();
            }


            $validationCode= new ValidationCode(['user_app_has_customer_id' => $userAppHasCustomer->user_app_has_customer_id]);

            $validationCode->save();

            $email_validator= new EmailValidator();

            if($validationCode->sendCodeSms($data['destinatary'])){

                return [
                    'status' => 'success',
                    'message' => Yii::t('app','Validation code has been sended to {phone}', ['phone' => $data['destinatary']]),
                ];
            }

            if ($email_validator->validate($data['destinatary']) && $validationCode->sendEmail()){
                return [
                    'status' => 'success',
                    'message' => Yii::t('app','Validation code has been sended {email}', ['email' => $model->email]),
                ];
            }

        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'status' => 'error',
            'message' => 'Cant send the validation code',
        ];
    }


    /**
     * Actualiza los datos del cliente
     * @return array
     */
    public function actionUpdate(){
        $data= Yii::$app->request->getBodyParams();

        //seteo como user el user de la api, porque es usado para guardar el log de actualizaciones de campos
        //TODO: consultar a Carlitos si es necesario guardar el id de que cliente actualizó los datos
        $user = User::findOne(['auth_key' => 'YXBpX3VzcjozPEFHTDExQ0g4WD8']);
        Yii::$app->user->setIdentity($user);

        $userApp= $this->getUserApp();

        $customer = Customer::findOne(['code' => $data['code']]);

        if ($customer && $userApp->hasCustomer($customer->code)){
            unset($data['code']);
            $old_email= $customer->email;
            if ( $customer->load($data, '') && $customer->save()){
                if ($customer->email !== $old_email && $userApp->email === $old_email){
                    $userApp->email = $customer->email;
                    $userApp->updateAttributes(['email']);
                }
                return [
                    'status' => 'success',
                    'customer' => $customer,
                ];
            }
            Yii::info($customer->getErrors(), 'CUSTOMER-MODEL-ERROR');
        }

        Yii::$app->response->setStatusCode(400);

        return [
            'status' => 'error',
            'errors' => Yii::t('app','Customer not found')
        ];
    }

    /**
     * @param $pdf_key
     * @param $email
     * @return array
     * @throws \Exception
     * Envía un mail con un pdf del comprobante
     */
    public function actionSendBillEmail($pdf_key, $email){

        $id= $this->encrypt_decrypt($pdf_key, 'decrypt');

        $bill = Bill::findOne($id);

        if (empty($bill)){
            Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'error',
                'error' => Yii::t('app','Bill not found'),
            ];
        }

        $pointOfSale = $bill->getPointOfSale()->number;

        $pdf = $this->actionPdf($id);

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
                    'image'         => Yii::getAlias("@app/web/". $bill->customer->parentCompany->getLogoWebPath()),
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
     * Verifica si el cliente posee un documento valido en la base de datos
     * Si posee un documento valido devuelve el tipo de documento que tiene asociado
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionVerifyCustomer(){
        $data= Yii::$app->request->getBodyParams();

        if (!isset($data['customer_code'])){
            throw new BadRequestHttpException('Customer code required');
        }

        $customer= Customer::findOne(['code' => $data['customer_code'] ]);

        if (empty($customer)){
            throw new NotFoundHttpException('Customer not found');
        }

        Yii::$app->response->format= Response::FORMAT_JSON;

        if ($customer->documentType && !($customer->document_number == '0' || empty($customer->document_number))){
            $document_type= [
                'document_type_id' => $customer->documentType->document_type_id,
                'name' => strtoupper($customer->documentType->name),
                'has_document_number' => true,
            ];
        }else{
            $document_type= [
                'document_type_id' => null,
                'name' => 'NONE',
                'has_document_number' => false,
            ];
        }

        return [
            'status' => 'success',
            'document_type' =>$document_type,
            //'destinataries' => $customer->destinataries,
        ];
    }

    /**
     * Devuelve los destinatarios del cliente para enviarle el codigo de valicion
     *
     * Recibe el customer_code y el email
     *
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCustomerData(){
        $data= Yii::$app->request->getBodyParams();

        if (!isset($data['customer_code']) || !isset($data['email'])){
            throw new BadRequestHttpException(Yii::t('app','Customer code and email are required'));
        }

        $customer = Customer::find()
            ->andWhere(['code' => $data['customer_code'], 'status' => 'enabled'])
            ->andWhere('email ="'.$data['email'].'" OR email2="'.$data['email'].'"')
            ->one();

        if ($customer){
            return [
                'status' => 'success',
                'destinataries' => $customer->destinataries,
            ];
        }

        Yii::$app->response->setStatusCode(400);

        return[
            'status' => 'error',
            'error' => Yii::t('app','Email and customer code not correspond to an active customer')
        ];
    }

    /**
     *
     * Similiar al send-validation-code con la diferencia que no pide el user_app_id, debido a que no existe
     * Crea el user_app dejandolo en pendiente, el cual hasta que no complete el login completo no quedara activo
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionVerifyData(){
        $data= Yii::$app->request->getBodyParams();

        if (!isset($data['customer_code']) || !isset($data['destinatary'])){
            throw new BadRequestHttpException(Yii::t('app','Customer code and destinatary are required'));
        }

        $userApp= new UserApp([
           'destinatary' => $data['destinatary'],
        ]);

        if ($userApp->save() && Customer::find()->andWhere(['code' => $data['customer_code']])->exists()){

            $userAppHasCustomer= new UserAppHasCustomer([
                'customer_code' => $data['customer_code'],
                'user_app_id' => $userApp->user_app_id
            ]);

            if ($userAppHasCustomer->save()){
                $validationCode= new ValidationCode(['user_app_has_customer_id' => $userAppHasCustomer->user_app_has_customer_id]);

                $validationCode->save();
                $validator= new EmailValidator();

                if (!$validator->validate($data['destinatary'])){

                    if($validationCode->sendCodeSms($data['destinatary'])){
                        return [
                            'status' => 'success',
                            'message' => Yii::t('app','Validation code has been sended to {phone}', ['phone' => $data['destinatary']]),
                        ];

                    }
                    Yii::info('Sms no enviado');
                }else{

                    if ($validationCode->sendEmail()){
                        return [
                            'status' => 'success',
                            'message' => Yii::t('app','Validation code has been sended {email}', ['email' => $userApp->email]),
                        ];
                    }
                    Yii::info('email no enviado');
                }
            }
        }


        Yii::$app->response->format= Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode(400);
        return [
            'status' => 'error',
            'error' => Yii::t('app','Cant verify data')
        ];
    }

    /**
     * Setea el documento al customer, previa validacion del codigo enviado. Si el resultado es positivo
     * el user_app esta en condiciones de poder loguearse normalmente. El user_app no queda logueado aun
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSetDocumentNumber(){
        $data= Yii::$app->request->getBodyParams();

        if (!isset($data['validation_code']) && !isset($data['document_number']) && !isset($data['customer_code'])){
            throw new BadRequestHttpException('Validation Code document number and customer code are required');
        }

        $validationCode= ValidationCode::find()
            ->andWhere(['code' => $data['validation_code']])
            ->andWhere(['>=', 'expire_timestamp', time()])
            ->orderBy(['validation_code_id' => SORT_DESC])
            ->one();

        if ($validationCode && $validationCode->userAppHasCustomer->customer_code == $data['customer_code']){
            $customer= Customer::findOne(['code' => $data['customer_code']]);

            if ($customer){
                $customer->document_number = $data['document_number'];

                if ($customer->validate(['document_number']) && $customer->updateAttributes(['document_number'])){
                    return [
                        'status' => 'success',
                        'message' => Yii::t('app','Document number saved successfull')
                    ];
                }

                \Yii::$app->response->setStatusCode(400);
                return [
                    'status' => 'error',
                    'errors' =>  $customer->getErrors(),
                ];
            }
            \Yii::$app->response->setStatusCode(400);
            return [
                'status' => 'error',
                'error' =>  Yii::t('app','Customer Not Found'),
            ];
        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'status' => 'error',
            'error' => Yii::t('app','Cant validate code'),
        ];

    }

    /**
     * Registra los login fallidos para poder comunicarse con el cliente posteriormente
     * @return array
     */
    public function actionCreateAppFailedRegister(){
        $data = Yii::$app->request->getBodyParams();

        $failed_register= new AppFailedRegister();

        if ($failed_register->load($data, '') && $failed_register->save()){
            return [
                'status' => 'success',
                'message' => Yii::t('app','Data has been saved succesfull')
            ];
        }

        return [
          'status' => 'error',
          'errors' => $failed_register->getErrors()
        ];
    }

    /**
     * Devuelve la info de contacto para mostrar en la pantalla de login
     * @return array
     */
    public function actionGetContactInfo()
    {
        $info = Config::getValue('app_contact_info');
        $tecnico = Config::getValue('app_ws_tecnico');
        $admin = Config::getValue('app_ws_admin');
        $ventas = Config::getValue('app_ws_ventas');

        return [
            'status' => 'success',
            'info' => $info,
            'tecnico' => $tecnico,
            'admin' => $admin,
            'ventas' => $ventas
        ];
    }
}