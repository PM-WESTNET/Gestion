<?php

namespace app\modules\sale\controllers;

use app\components\helpers\ExcelExporter;
use app\components\helpers\PDFService;
use app\components\web\Controller;
use app\modules\afip\components\CuitOnlineValidator;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\invoice\components\einvoice\ApiFactory;
use app\modules\sale\models\Address;
use app\modules\sale\models\Category;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerMessage;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\ticket\models\Ticket;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use PHPExcel_Style_NumberFormat;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use SoapClient;
use yii\web\UploadedFile;
use yii2fullcalendar\yii2fullcalendar;
use app\modules\sale\models\Product;
use app\modules\westnet\models\Vendor;
use app\modules\westnet\reports\models\ReportChangeCompany;
use app\modules\config\models\Config;
use app\modules\mailing\components\sender\MailSender;
use app\modules\mailing\models\EmailTransport;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{
    public $freeAccessActions = ['barcode'];
    
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';
        $searchModel = new CustomerSearch;

        if(empty($_GET['search_text']) || (isset($_GET['search_text']) && isset($_GET['CustomerSearch']))){
            if(isset($_GET['CustomerSearch'])){
                $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
            } else {
                $query = Customer::find()->where(['name' => '||']);
                $dataProvider = new ActiveDataProvider(['query' => $query]);
            }
        }else{
            $searchModel->search_text= $_GET['search_text'];
            $dataProvider = $searchModel->searchText(['CustomerSearch' => ['search_text' => $_GET['search_text']] ]);          
        }

        $categoriesPlan = ArrayHelper::map(Category::find()->andWhere(['IN', 'name', ['Plan fibra', 'Plan wifi']])->all(), 'category_id', 'name');
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'categoriesPlan' => $categoriesPlan
        ]);
    }
    
     public function actionExportIndex()
    {
        set_time_limit(0);
        if(empty($_GET['search_text']) || (isset($_GET['search_text']) && isset($_GET['CustomerSearch']))){
            if(isset($_GET['CustomerSearch'])){
                $searchModel = new CustomerSearch;
                $customers= $searchModel->searchForExport(Yii::$app->request->getQueryParams())->all();
            } else {
                $searchModel = new CustomerSearch;
                $query = Customer::find()->where(['name' => '||']);
                $customers = $query->all();
            }
        }else{
            $searchModel = new CustomerSearch;
            $searchModel->search_text= $_GET['search_text'];
            $customers = $searchModel->searchText(['CustomerSearch' => ['search_text' => $_GET['search_text']] ])->query->all();          
        }
        
        $excel= ExcelExporter::getInstance();
        $excel->create('Clientes', [
            'A' => ['code', Yii::t('app', 'Customer Number'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'B' => ['name', Yii::t('app', 'Customer'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'C' => ['document_number', Yii::t('app', 'Document Number'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'D' => ['phone', Yii::t('app', 'Phone'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'E' => ['phone2', Yii::t('app', 'Second Phone'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'F' => ['phone3', Yii::t('app', 'Third Phone'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'G' => ['phone4', Yii::t('app', 'Cellphone 4'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
	        'H' => ['email', Yii::t('app', 'Email'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'I' => ['email2', Yii::t('app', 'Secondary Email'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'J' => ['class', Yii::t('app', 'Customer Class'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'K' => ['category', Yii::t('app', 'Customer Category'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'L' => ['company', Yii::t('app', 'Company'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
        ])->createHeader();       
        
        foreach ($customers as $c) {
            $excel->writeRow([
                'code'=> $c['code'],
                'name'=> (!($c instanceof Customer) ? $c['name'] : $c->fullName),
                'document_number' => $c['document_number'],
                'phone' => $c['phone'],
                'phone2' => $c['phone2'],
                'phone3' => $c['phone3'],
                'phone4' => $c['phone4'],
                'email' => $c['email'],
                'email2' => $c['email2'],
                'class' => (!($c instanceof Customer) ? $c['class'] : $c->customerClass->name),
                'category' => (!($c instanceof Customer) ? $c['category'] : $c->customerCategory->name),
                'company' => (!($c instanceof Customer) ? $c['company'] : $c->company->name),
            ]);
        }
        
        $excel->download('Clientes.xls');      
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionSearch($term)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $searchModel = new CustomerSearch;
        $dataProvider = $searchModel->searchText(['CustomerSearch' => ['search_text' => $term] ]);
        
        $map = array_map(function($model){
            
            return [
                'label' => $model->fullName,
                'value' => $model->customer_id
            ];
            
        }, $dataProvider->getModels());
        
        return $map;
        
    }

    
    /**
     * Displays a single Customer model.
     * TODO: fix address
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $model = $this->findModel($id);
        $address = $model->address;
        if($model->canView()){
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'status' => 'success',
                    'model' => $model
                ];
            }

            if($model->needsUpdate) {
                Yii::$app->session->setFlash('warning', Yii::t('app','This customer needs to confirm data. Last update: {date}', ['date' => ( new \DateTime($model->last_update))->format('d-m-Y')]));
            }
            $contracts = ContractSearch::getdataProviderContract($model->customer_id);
            $messages = CustomerMessage::find()->andWhere(['status' => CustomerMessage::STATUS_ENABLED])->all();

            $products = ArrayHelper::map(Product::find()
                ->andWhere(['type' => 'product'])
                ->andWhere(['LIKE', 'name', 'Recargo por Extensión de Pago'])
                ->all(), 'product_id', 'name');

            $vendors = ArrayHelper::map(Vendor::find()->leftJoin('user', 'user.id=vendor.user_id')
                ->andWhere(['OR',['IS', 'user.status', null], ['user.status' => 1]])
                ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])
                ->all(), 'vendor_id', 'fullName');

            $url_whatsapp = Config::getConfig('siro_url_payment_button_whatsapp')->item->description;

            return $this->render('view', [
                'model' => $model,
                'address'=> $address,
                'contracts' => $contracts,
                'messages' => $messages,
                'products' => $products,
                'vendors' => $vendors,
                'url_whatsapp' => $url_whatsapp
            ]);
        }else{
           throw new ForbiddenHttpException(\Yii::t('app', 'You can`t do this action'));
        }
        
    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer;
        $address= new Address;
        $address->scenario = 'insert';
        $model->scenario= 'insert';

        if ($model->load(Yii::$app->request->post()) && $address->load(Yii::$app->request->post())) {
            if($address->save()){
                $model->setAddress($address);
                $this->upload($model, 'document_image');
                $this->upload($model, 'tax_image');

                if($model->save()){
                    if(Yii::$app->params['plan_product']){
                        return $this->redirect(['/sale/contract/contract/create', 'customer_id' => $model->customer_id]);
                    }
                    else{
                        return $this->redirect(['view', 'id' => $model->customer_id]);
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'address'=> $address,
        ]);

    }

    /**
     * Creates a new Customer model from a Iframe.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateEmbed()
    {
        $this->layout = '//embed';
        
        $model = new Customer;

        if ($model->load(Yii::$app->request->post())) {
            
            $model->save();
            
            return $this->render('create-embed', [
                'model' => $model,
            ]);
            //return $this->redirect(['view', 'id' => $model->customer_id]);
        } else {
            
            //Para poder inicializar algunos attrs desde la url
            $model->load(Yii::$app->request->get());
            
            return $this->render('create-embed', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $address=  $model->address ? $model->address : new Address;
        $document_image = $model->document_image;
        $tax_image = $model->tax_image;
        $docImageUpdate = Yii::$app->request->post('document_image_update', 0);
        $taxImageUpdate =  Yii::$app->request->post('tax_image_update', 0);

        if($model->canUpdate()){
            if ($model->load(Yii::$app->request->post()) && $address->load(Yii::$app->request->post())) {

                if ($docImageUpdate) {
                    $this->upload($model, 'document_image');
                } else {
                    $model->document_image = $document_image;
                }

                if ($taxImageUpdate) {
                    $this->upload($model, 'tax_image');
                } else {
                    $model->tax_image = $tax_image;
                }

                if ($address->save()) {

                    $model->setAddress($address);
                    if ($model->save()) {

                        return $this->redirect(['view', 'id' => $model->customer_id]);
                    }
                }
            }
            return $this->render('update', [
                        'model' => $model,
                        'address' => $address,
            ]);
        }else{
            throw new ForbiddenHttpException(\Yii::t('app', 'You can`t do this action'));
        }

    }
    
    /**
     * Muestra historial de categorías del Cliente
     */
    public function actionClasshistory($id)
    {
        return $this->render('_classhistory', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionCreatecontract($id)
    {
        return $this->render('/contract/contract/create', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * 
     */
    public function actionSell()
    {
        
        $model = new CustomerSearch;
        
        return $this->render('sell', [
            'model' => $model
        ]);
        
    }
    
    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Muestra un listado con las deudas de los clientes.
     *
     * @return mixed
     */
    public function actionDebtors()
    {
        $searchModel = new CustomerSearch;
        $dataProvider = $searchModel->searchDebtors(Yii::$app->request->getQueryParams());

        return $this->render('debtors', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    /**
     * Exporta a excel los datos de la vista de deudores
     */
    public function actionExportDebtors()
    {
        $searchModel= new CustomerSearch();
        $debtors= $searchModel->buildDebtorsQuery(Yii::$app->request->getQueryParams())->all();
        
        $excel= ExcelExporter::getInstance();
        $excel->create('Deudores', [
            'A' => ['code', Yii::t('app', 'Customer Number'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'B' => ['name', Yii::t('app', 'Customer'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'C' => ['phone', Yii::t('app', 'Phone'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'D' => ['phone2', Yii::t('app', 'Phone 2'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'E' => ['phone3', Yii::t('app', 'Phone 3'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'F' => ['phone4', Yii::t('app', 'Phone 4'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'G' => ['saldo', Yii::t('app', 'Amount due'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
            'H' => ['debt_bills', Yii::t('app', 'Debt Bills'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            
        ])->createHeader();
        
        $excel->writeData($debtors);
        
        $excel->download('Deudores.xls');
        
        
    }
    
    /**
     * Muestra un listado con los clientes con saldo a favor
     * @return type
     */
    public function actionPositiveBalanceCustomers(){
        $customerSearch = new CustomerSearch();
        $customerSearch->amount_due = null;

        $queryParms = Yii::$app->request->getQueryParams();
        if(array_key_exists('CustomerSearch', $queryParms)) {
            $queryParms['CustomerSearch']['amount_due_to'] = -abs($queryParms['CustomerSearch']['amount_due_to']);
        }else{
            $queryParms['CustomerSearch']['amount_due_to'] = 0;
        }

        $subQuery = $customerSearch->buildDebtorsQuery($queryParms);
        $positiveBalanceCustomers= new ActiveDataProvider(['query' => $subQuery]);
        $totalBalance = $subQuery->sum('saldo');
        
        
        return $this->render('positive-balance-customers', ['provider' => $positiveBalanceCustomers, 'search' => $customerSearch, 'totalBalance' => $totalBalance]);
        
    }
    
    /**
     * Exporta a excel los datos de la pantalla de clientes con saldo a favor
     */
    public function actionExportPositiveBalanceCustomers()
    {
        $customerSearch = new CustomerSearch();
        $customerSearch->amount_due = null;

        $queryParms = Yii::$app->request->getQueryParams();
        if(array_key_exists('CustomerSearch', $queryParms)) {
            $queryParms['CustomerSearch']['amount_due_to'] = -abs($queryParms['CustomerSearch']['amount_due_to']);
        }else{
            $queryParms['CustomerSearch']['amount_due_to'] = 0;
        }

        $positiveBalanceCustomers = $customerSearch->buildDebtorsQuery($queryParms)->all();
        
        foreach ($positiveBalanceCustomers as $key=>$c){
            $positiveBalanceCustomers[$key]['saldo']= (-1) * (double)$c['saldo'];
        }
        
        $excel= ExcelExporter::getInstance();
        $excel->create('Clientes-Saldo-A-Favor', [
            'A' => ['code', Yii::t('app', 'Customer Number'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'B' => ['name', Yii::t('app', 'Customer'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'C' => ['phone', Yii::t('app', 'Phone'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'D' => ['saldo', Yii::t('app', 'Balance'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],           
        ])->createHeader();
        
        $excel->writeData($positiveBalanceCustomers);
        
        $excel->download('Clientes-Saldo-A-Favor.xls');
        
        
    }

    /**
     * Busca customer por nombre
     * @param $name
     * @return array
     */
    public function actionFindByName($name, $id=null, $normal= true)
    {
        Yii::$app->response->format = 'json';

        if(!is_null($name)) {
            $searchModel = new CustomerSearch;

            $data['results'] = $searchModel->searchByName($name)
                ->select([((bool)$normal ? 'customer_id as id' : 'code as id'),
                    "CONCAT(customer.code, ' - ', lastname, ' ', customer.name) as text"])
                ->asArray()->all();
        } else if( $id > 0) {
            $data['results'] = ['id' => $id, 'text' => Customer::find($id)->name];
        }

        return $data;
    }

    /**
     * Muestra los descuentos asignados al cliente
     * @param $customer_id
     */
    public function actionDiscounts($id)
    {
        return $this->render('_discounts', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Cambia la empresa del cliente
     * @param type $customer_id
     * @param type $company_id
     * @return type
     */
    public function actionChangeCompany($customer_id, $company_id ){
        
        $model= $this->findModel($customer_id);

        $reportChangeCompany = new ReportChangeCompany();

        $reportChangeCompany->customer_id_customer = $customer_id;
        $reportChangeCompany->new_business_name = Company::FindCompanyByID($company_id)['name'];
        $reportChangeCompany->old_business_name = $model->company->name;
        $reportChangeCompany->date = Date("Y-m-d");
        
        /**if ($model->lastname == '') {
            $model->lastname= ' - ';
        }

        if ($model->document_type_id == NULL) {
            $model->document_type_id = 2;
        }**/
        Yii::$app->response->format= Response::FORMAT_JSON;
        // Lleno old_company_id
        
        $model->company_id = $company_id;
        
        if ($model->save(false)) {
            if($reportChangeCompany->new_business_name != $reportChangeCompany->old_business_name)
                $reportChangeCompany->save();
            return ['status' => 'success'];
        }else{
            return ['status' => 'error', 'message' => $model->getErrors() ];
        }
        
    }
    
    /**
     * Muestra los contratos pendientes de instalacion.
     * @return type
     */
    public function actionPendingInstallations(){
        $this->layout = '//fluid';
        $searchModel= new ContractSearch();        
        
        $ads= $searchModel->searchWithoutConnections(Yii::$app->request->getQueryParams());
        
        return $this->render('pending-installations', ['ads' => $ads, 'searchModel' => $searchModel]);
    }
    
    /**
     * Muestra los tickets de un cliente en especifico
     * @param type $id
     * @return type
     */
    public function actionCustomerTickets($id){
        $query = Ticket::find()->where(['customer_id' => $id]);
                
        $tickets= new ActiveDataProvider(['query' => $query]);
        
        $customer= $this->findModel($id);
        
        return $this->render('customer-tickets', ['tickets'=> $tickets, 'customer' => $customer]);
    }
    
    /**
     * Carnet del cliente con codigo de pago.
     * @param int $id
     * @param boolean $web
     * @return pdf
     */
    public function actionCustomerCarnet($id, $web = false){
        
        $this->layout = '//pdf';
        $model= $this->findModel($id);
        
        if (!empty($model->payment_code)) {
            if(strlen($model->company->code)<4) {
                $code = (string)"0".$model->payment_code;
            }else{
                $code = (string)$model->payment_code;
            }
        }else{
            $code = $model->code;
        }
        
        $view = $this->render('customer-carnet', ['name' => $model->fullName, 'code'=> $code, 'company' => $model->parentCompany]);
        
        //Si $web es false, devolvemos un pdf
        if(!$web){
            
            $response = Yii::$app->getResponse();
            $response->format = Response::FORMAT_RAW;
            $response->headers->set('Content-type: application/pdf');
            $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);
            
            return PDFService::makePdf($view);
        }
        
        return $view;        
        
    }
        
        
    public function actionBarcode($code){
        $barcode = new Barcode();
        $barcode->setGenbarcodeLocation(Yii::$app->params['genbarcode_location']);
        $barcode->setMode(Barcode::MODE_PNG);
        $barcode->setEncoding(Barcode::ENCODING_ANY);
        
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = Response::FORMAT_RAW;
        
        return $barcode->outputImage($code);
    }

    public function actionBilledAndCashed()
    {
        $billSearch = new BillSearch();
        $paymentSearch = new PaymentSearch();


        return $this->render('billed-and-cashed', [
            'billed' => $billSearch->searchBilledByDate(Yii::$app->request->getQueryParams()),
            'cashed' => $paymentSearch->searchCashedByDate([
                'PaymentSearch'=>[
                    'from'=> $billSearch->fromDate,
                    'to'=>$billSearch->toDate
                ]
            ]),
            'searchModel' => $billSearch
        ]);
    }
    
    public function actionInstallations()
    {
        $contract_search= new ContractSearch();
        $customer_search= new CustomerSearch();
        $params= Yii::$app->request->getQueryParams();
        
        
        if (!isset($params['ContractSearch'])) {
            $contract_search->from_date= date('Y-m-d', strtotime('-7 days'));
            $contract_search->to_date= date('Y-m-d');
        }
        
        $billsQuery= $customer_search->searchAllBills();
        $ticketQuery =$customer_search->getTicketsCount();
        $installations= $contract_search->getInstallations($params, $billsQuery, $ticketQuery);

        $dataProvider= new ActiveDataProvider(['query' => $installations]);
        $users = ArrayHelper::map(User::find()->where(['status' => 1])->all(), 'id', 'username');

        $this->layout= '//fluid';
        return $this->render('installations', ['data' => $dataProvider, 'contract_search' => $contract_search, 'users' => $users]);
    }

    public function actionAfipValidation($document)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Obtengo la session para ver si tengo guardado algun token
        $session = Yii::$app->session;

        $params = Yii::$app->params['afip-validation'];

        /** @var CuitOnlineValidator $api */
        $api = ApiFactory::getInstance()->getApi(CuitOnlineValidator::class);
        $company = Company::findOne(['company_id'=> $params['company_id']]);

        $valid_data = '';
        $final_data = '';

        $api->setCompany($company);
        $api->setTesting($params['testing']);
        $api->setUseOnline($params['use-online']);
        $api->setSaveCalls($params['save-calls']);
        if ($session->has("afip_token")) {
            $api->setTokens($session->get("afip_token"));
        }
        try {
            if (!$api->isTokenValid()) {
                $certificate = Yii::getAlias('@webroot') . '/' . $company->certificate;
                $key = Yii::getAlias('@webroot') . '/' . $company->key;
                $authorize = $api->authorize($certificate, $key, $company->certificate_phrase);
                $session->set("afip_token", $api->getTokens());
            }
            if ($api->isTokenValid() || $authorize) {
                error_log("4");

                if ($api->connect([], ["ssl" => ["ciphers" => "TLSv1"]], 'SOAP_1_1')) {
                    error_log("5");
                    \Yii::trace('se conecta a api');
                    $valid_data = $api->validate(str_replace('-', '', $document));
                    $final_data = $api->extractData($valid_data);
                }
            }
        } catch (\Exception $ex) {
            $errors[] = [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];
        }

        return [
            'status' => $valid_data ? true : false,
            'data' => $final_data
        ];
    }

    public function actionValidateCustomer()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $customer = new Customer();

            if ($customer->load(Yii::$app->request->post())) {
                $validate = $customer->validateCustomer();
                if ($validate !== false) {
                    return $validate;
                }
            }

            return [
                'status' => 'error',
            ];
        }

        throw new NotFoundHttpException('Page Not Found');
    }

    /**
     * @return string
     * Renderiza el panel de cobranza
     */
    public function actionCashingPanel()
    {
        $searchModel = new CustomerSearch;
        $searchModel->exclude_customers_with_one_bill = true;
        $searchModel->not_contract_status = 'low';
        $dataProvider = $searchModel->searchDebtors(Yii::$app->request->getQueryParams(), 100);

        Yii::$app->session->setFlash('info', Yii::t('app', 'Remember: Customers whose debt is on the first bill and their contract is in low status are excluded'));

        return $this->render('cashing-panel', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * Envía mensajes sms predeterminados al cliente.
     */
    public function actionSendMessage() {
        $data = Yii::$app->request->get();

        if (!isset($data['customer_id']) || !isset($data['customer_message_id'])) {
            throw new BadRequestHttpException('Customer ID and Customer Message ID are required');
        }

        $customer = $this->findModel($data['customer_id']);
        $message = CustomerMessage::findOne($data['customer_message_id']);

        if (empty($message)) {
            throw new BadRequestHttpException('Message not found');
        }

        if (isset($data['phones']) && !empty($data['phones'])) {
            $response = $message->send($customer, $data['phones']);
        }else{
            $response = $message->send($customer);
        }


        foreach ($response['alerts'] as $alert) {
            if ($alert['status'] === 'success'){
                Yii::$app->session->addFlash('success', Yii::t('app','Message sended to phone {phone} successfull', ['phone' => $alert['phone']]));
            }else {
                Yii::$app->session->addFlash('error', Yii::t('app','Can`t send message to phone {phone}', ['phone' => $alert['phone']]));
            }
        }

        return $this->redirect(['view', 'id' => $customer->customer_id]);
    }

    public function actionVerifyEmails()
    {
        $results = [];

        if (Yii::$app->request->isPost) {
            $files = UploadedFile::getInstancesByName('files');
            if (empty($files)) {
                Yii::$app->session->addFlash('error', Yii::t('app','You must select at least a file'));
                return $this->render('verify-emails', ['results' => $results]);
            }


            foreach ($files as $file) {
                $resource = fopen($file->tempName, 'r');

                if ($resource === false) {
                    Yii::$app->session->addFlash('error', Yii::t('app','Cant open files'));
                    return $this->render('verify-emails', ['results' => $results]);
                }

                $partial_result = Customer::verifyEmails($resource, Yii::$app->request->post('field'), Yii::$app->request->post('type'));

                foreach ($partial_result as $key => $r) {
                    if (isset($results[$key])) {
                        $results[$key] = $results[$key] + $r;
                    }else {
                        $results[$key] = $r;
                    }
                }
            }
        }

        return $this->render('verify-emails', ['results' => $results]);
    }

    private function upload($model, $attr){

        $file = UploadedFile::getInstance($model, $attr);

        $folder = \yii\helpers\Inflector::pluralize($attr);

        if ($file && $model->validate()) {
            $filePath = Yii::$app->params['upload_directory'] . "$folder/". uniqid($attr) . '.' . $file->extension;

            if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
            }

            $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);

            $model->$attr = $filePath;

            return true;
        } else {
            return false;
        }

    }

    public function actionSendPaymentButtonEmail($email,$customer_id){
        Yii::$app->response->format = 'json';
        
        $url_redirect_gestion = Config::getConfig('siro_url_redirect_gestion')->item->description;
        $content_email = Config::getConfig('siro_content_email_payment_button')->item->description;
        $subject_email = Config::getConfig('siro_subject_email_payment_button')->item->description;
        
        $transport = EmailTransport::FindEmailTransportByNotificacion();
        Yii::$app->mail->setTransport($transport->getConfigArray());

        $mailer = Yii::$app->mail;
        $mailer->htmlLayout = '@app/modules/westnet/notifications/body/layouts/PaymentButton';
        $params = ['emailTransport' => $transport,
                    'subject' => $subject_email,
                    'content' => "<div style='text-align:center'>".$content_email."<br><button style='background-color:orange;border-radius:0.5em;height:2.5em;transition-duration: 0.4s;'><a href=".str_replace('${customer_id}',$customer_id,$url_redirect_gestion). " style='color:black;text-decoration:none;'>Botón de Pago</a></button></div>"
            ];
        Yii::$app->view->params['notification'] = $params; 

        $message = $mailer
                ->compose('@app/modules/westnet/notifications/body/layouts/PaymentButton')
                ->setFrom($transport->from_email)
                ->setTo($email)
                ->setSubject($subject_email);
        
        if($message->send()){
            Yii::$app->session->setFlash('success', 'Correo enviado correctamente a ' . $email);
            $this->redirect(['view','id' => $customer_id]);
        }else{
            Yii::$app->session->setFlash('error', 'Ha ocurrido un error y el correo no ha podido ser enviado.');
            $this->redirect(['view','id' => $customer_id]);
        }
    }
}