<?php

namespace app\modules\sale\controllers;

use app\modules\mailing\components\sender\MailSender;
use app\modules\mailing\services\ConfigMailing;
use app\modules\sale\models\BillDetail;
use app\modules\sale\models\Company;
use app\modules\sale\models\StockMovement;
use Hackzilla\BarcodeBundle\Utility\Barcode;
use kartik\mpdf\Pdf;
use Yii;
use app\modules\sale\models\Bill;
use app\modules\sale\models\search\BillSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sale\components\BillExpert;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use Da\QrCode\QrCode;

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Profile;
use app\modules\sale\models\TaxRate;
use app\modules\config\models\Config;
use yii\helpers\Url;
use Picqer\Barcode\BarcodeGeneratorPNG;
use yii\filters\AccessControl;

/**
 * BillController implements the CRUD actions for Bill model.
 */
class BillController extends Controller
{

    /**
     * Las reglas para 'create', 'update', 'open' y 'delete', se establecen a
     * través de permisos especificos y consideran el tipo de comprobante, por
     * lo tanto damos acceso libre para que el modulo de control de acceso no
     * las intervenga.
     * Barcode es de acceso libre por conveniencia (genera una imagen).
     */
    public $freeAccessActions = ['barcode', 'create', 'update', 'open', 'delete'];

    public function behaviors()
    {
        return array_merge(parent::behaviors(),['access' => [
            'class' => AccessControl::class,
            'only' => ['email-console'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['?'],
                ],
            ],
        ],
        ]);
    }

    /**
     * Lists all Bill models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';

        $searchModel = new BillSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Bill models.
     * @return mixed
     */
    public function actionGroup($footprint)
    {
        $searchModel = new BillSearch;
        $searchModel->active = null;
        $searchModel->footprint = $footprint;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Bill models.
     * @return mixed
     */
    public function actionHistory()
    {
        $searchModel = new BillSearch;

        $dataProvider = $searchModel->searchHistory(Yii::$app->request->getQueryParams());

        //Chart
        $graphData = new \app\components\helpers\GraphData();

        if ($searchModel->chartType != false) {

            $searchModel = new BillSearch;

            $graphDataProvider = $searchModel->searchHistory(Yii::$app->request->getQueryParams());
            $graphDataProvider->sort = false;
            $graphModels = $graphDataProvider->getModels();
            $first = array_shift($graphModels);
            $last = array_pop($graphModels);

            if ($first != null and $last != null) {
                $graphData->fromdate = $first->date;
                $graphData->todate = $last->date;
            }

            $graphData->steps = \yii\helpers\ArrayHelper::getColumn($graphModels, 'date');

            //Datos
            $graphData->dataProvider = $graphDataProvider;
            $graphData->yAttribute = 'total';
            $graphData->xAttribute = 'date';
            $graphData->colorAttribute = 'rgb';
            $graphData->idAttribute = 'bill_id';
        }

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'graphData' => $graphData
        ]);
    }

    /**
     * Displays a single Bill model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(isset(Yii::$app->request->post()['close-bill'])){
            if($model->total > 0)
                    $this->close($id);      
                else
                    Yii::$app->session->setFlash('error','No pueden cerrarse facturas con un monto igual a $0.');
        }
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new Bill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type, $customer_id = null, $company_id = null)
    {
        $model = BillExpert::createBill($type);
        $company = $model->billType->getCompanies()->one();

        if (!$company || !$company->pointsOfSale) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'Default company or default point of sale not defined.'));
        }
        if (!$company_id) {
            $model->company_id = $company->company_id;
        } else {
            $model->company_id = $company_id;
        }
        if ($company->partner_distribution_model_id) {

            $model->partner_distribution_model_id = $company->partner_distribution_model_id;
        }

        if ($customer_id !== null) {
            $model->customer_id = $customer_id;
        }

        if (!$model->save()) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'Error saving the new bill.'));
        }

        $this->redirect(['update', 'id' => $model->bill_id]);

    }

    /**
     * Updates an existing Bill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $embed = false, $errors = '')
    {

        //Si se debe embeber la vista
        if ($embed == true) {
            $this->layout = '//embed';
        }

        $model = $this->findModel($id);
        $electronic_billing = 1;

        if ($errors) {
            $model = Bill::fillErrors($model, $errors);
        }

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        //Pueden ocurrir inconsistencias al abandonar la pantalla sin que se complete la accion (TODO: transactions; por ahora no quitar esta linea)
        $model->updateAmounts();

        //Al cambiar de clase, necesitamos reinstanciar y se informa al objeto del cambio
        $previousClass = $model->class;
        $company_id = $model->company_id ? $model->company_id : '';

        if ($model->load(Yii::$app->request->post())) {

            //Para evitar que el punto de venta no se actualice cuando se cambia la empresa.
            if($company_id != $model->company_id) {
                $model->point_of_sale_id = $model->company->getDefaultPointOfSale() ? $model->company->getDefaultPointOfSale()->point_of_sale_id : '';
            }

            //Fecha manual
            if (key_exists('Bill', Yii::$app->request->post())) {
                if (key_exists('date', Yii::$app->request->post('Bill'))) {
                    $date = new \DateTime(Yii::$app->request->post('Bill')['date']);
                    $model->updateAttributes([
                        'date' => $date->format('Y-m-d'),
                        'time' => (new \Datetime('now'))->format('H:i:s')
                    ]);
                }
            }
            $model->save();
            if(isset(Yii::$app->request->post()['close-bill'])){
                if($model->total > 0){
                    $this->close($id);      
                }
                else{
                    Yii::$app->session->addFlash('error','No pueden cerrarse facturas con un monto igual a $0.');
                }
            }
            //Si la clase cambio:
            if ($previousClass != $model->class) {
                //Debemos volver a instanciar
                $model = $this->findModel($id);
                $model->onTypeChange($previousClass);
            }
        } else {
            if (!$model->point_of_sale_id) {
                $model->updateAttributes(['point_of_sale_id' => $model->company->getDefaultPointOfSale()->point_of_sale_id]);
            }
            $model->validate(null, false);

        }

        $productSearch = new \app\modules\sale\models\search\ProductSearch();
        if (!in_array($model->status, ['draft', 'error'])) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'The bill is not a draft and could not be updated.'));
        }

        //Detalles
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'productSearch' => $productSearch,
            'embed' => $embed,
            'electronic_billing' => $model->isNumberAutomaticalyGenerated()
        ]);
    }

    /**
     * Reabre un comprobante, cambiando su estado de completed a draft.
     * @param integer $id
     * @return mixed
     */
    public function actionOpen($id, $embed = false)
    {

        //Si se debe embeber la vista
        if ($embed == true) {
            $this->layout = '//embed';
        }

        $model = $this->findModel($id);

        //Permisos para reabrir?
        if (!BillExpert::checkAccess('open', $model->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if (Yii::$app->request->post('continue')) {
            $model->open();
            return $this->redirect(['update', 'id' => $model->bill_id]);
        }

        if (!$model->isOpenable()) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'The bill is not completed and could not be opened.'));
        }

        return $this->render('open', [
            'model' => $model,
            'embed' => $embed
        ]);
    }

    /**
     * Busca productos de acuerdo de acuerdo al parametro $text.
     * En caso de encontrar un unico producto, y en caso de ser encontrado
     * a traves de su codigo de barras, el producto se agrega a la factura
     * identificada por el parametro $id. Esto es asi para permitir buscar
     * directamente por codigo de barras o texto desde un mismo campo de busqueda
     * @param type $id
     * @param type $text
     * @return type
     */
    public function actionSearchProduct($id, $text)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);

        $searchModel = new \app\modules\sale\models\search\ProductSearch;

        //Para stock:
        Yii::$app->set('company', $model->company);

        $searchModel->search_text = $text;
        $searchModel->status = 'enabled';
        $dataProvider = $searchModel->searchFlex();

        $dataProvider->pagination->pageSize = Yii::$app->params['bill_products_page_size'];
        $dataProvider->pagination->pageParam = 'page';

        /**
         * Este codigo produce un error en Windows:
         * if($dataProvider->totalCount == 1)
         * en ocaciones devuelve 2 a pesar de retornar un solo modelo
         */
        if (count($dataProvider->getModels()) == 1) {
            $product = $dataProvider->getModels()[0];

            if ($product->compareCode($text) == true) {

                //Permisos para actualizar?
                if (!BillExpert::checkAccess('update', $model->class)) {
                    throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }

                if ($product->getInStock($model->company)) {

                    $detail = $this->addProductDetail($id, $product);

                    return [
                        'status' => 'success',
                        'detail' => $detail,
                    ];

                } else {

                    return [
                        'status' => 'error',
                        'errors' => Yii::t('app', 'No stock.')
                    ];

                }

            }
        }

        $mustachePager = Yii::createObject([
            'class' => '\app\components\helpers\MustachePager',
            'pagination' => $dataProvider->pagination
        ]);

        //Seteamos stock company
        $models = [];
        foreach ($dataProvider->getModels() as $product) {
            $product->stockCompany = $model->company;
            $models[] = $product;
        }

        return [
            'status' => 'success',
            'items' => $models,
            'pages' => $mustachePager->getPages()
        ];

    }


    /**
     * Busca productos de acuerdo de acuerdo al parametro $text.
     * En caso de encontrar un unico producto, y en caso de ser encontrado
     * a traves de su codigo de barras, el producto se agrega a la factura
     * identificada por el parametro $id.
     * @param type $id
     * @param type $text
     * @return type
     */
    public function actionAddProduct($id, $product_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);

        //Para stock:
        Yii::$app->set('company', $model->company);

        $product = \app\modules\sale\models\Product::findOne($product_id);

        if ($product !== null) {

            //Permisos para actualizar?
            if (!BillExpert::checkAccess('update', $model->class)) {
                throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }

            if ($product->getInStock($model->company)) {

                $detail = $this->addProductDetail($id, $product);

                return [
                    'status' => 'success',
                    'detail' => $detail,
                ];

            } else {

                return [
                    'status' => 'error',
                    'errors' => Yii::t('app', 'No stock.')
                ];

            }
        } else {
            return [
                'status' => 'error',
                'errors' => Yii::t('app', 'Product not found.')
            ];
        }

    }

    public function actionSearchCustomer($id, $text)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $searchModel = new \app\modules\sale\models\search\CustomerSearch;
        $searchModel->search_text = $text;
        $dataProvider = $searchModel->searchFlex();

        $dataProvider->pagination->pageSize = Yii::$app->params['bill_customers_page_size'];
        $dataProvider->pagination->pageParam = 'page';

        $mustachePager = Yii::createObject([
            'class' => '\app\components\helpers\MustachePager',
            'pagination' => $dataProvider->pagination
        ]);

        return [
            'status' => 'success',
            'text' => $text,
            'items' => $dataProvider->getModels(),
            'pages' => $mustachePager->getPages()
        ];

    }

    public function actionSelectCustomer($id, $customer_id)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $customer = \app\modules\sale\models\Customer::findOne($customer_id);

        $model->setCustomer($customer);

        return [
            'status' => 'success',
            'customer' => $customer,
            'bill' => $model
        ];

    }

    public function close($id, $ajax = false, $payAfterClose = false)
    {	

        $model = $this->findModel($id);
    	\Yii::info("----------------------------------------", 'duplicados-afip');
    	\Yii::info("1) Entre en actionClose"
        ."ID: ".$id."\n"
        ."Ajax: ".$ajax."\n"
        ."PayAfterClose: ".$payAfterClose."\n"
        ."Status: ".$model->getAttributes()['status']
        , 'duplicados-afip');


        if (!empty($model->billDetails) && $model->status != 'closed') {
            // try to close bill
            if (!$model->close()) {
                // error management code..
                $keys = Bill::getConcatedKeyErrors($model);
                $model->updateAttributes(['had_error' => true]);
                if(isset(Yii::$app->session)){ //* extremelly important bcause redirect() deletes session flashes. USED ON UPDATE.php VIEW
                    // add flashes before redirect deletes the session variable
                    $flashes = yii::$app->session->getAllFlashes();
                    yii::$app->session->removeAllFlashes();
                    yii::$app->session->set('customFlashes', $flashes); // adds a custom _SESSION[] variable called 'customFlashes'
                    
                }
                if ($keys) {
                    return $this->redirect(['update', 'id' => $model->bill_id, 'embed' => false, 'errors' => $keys]);
                } else {
                    return $this->redirect(['update', 'id' => $model->bill_id, 'embed' => false]);
                }
            }//else: the bill was closed without errors.
        }

        if (!$ajax) {
            if ($payAfterClose) {
                return $this->redirect(['/checkout/payment/pay-bill', 'bill' => $model->bill_id]);
            } else {
                return $this->redirect(['bill/view', 'id' => $id]);
            }
        } else {

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'status' => 'success',
                'bill' => $model,
            ];
        }
    }

    /**
     * Genera un nuevo comprobante
     * @param int $id
     * @param int $type
     * @throws \yii\web\HttpException
     * @throws HttpException
     */
    public function actionGenerate($id, $type)
    {
        $model = $this->findModel($id);
        $billType = \app\modules\sale\models\BillType::findOne($type);

        if (!$model->active) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'This bill is not actived.'));
        }

        if (!$model->customer->checkBillType($billType)) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'This customer could not be billed with the type selected.'));
        }

        //Detalles q deben ser considerados al generar el nuevo comprobante
        $details = Yii::$app->request->get('details');
        if (empty($details)) {
            $details = [];
        }

        //Generate Here
        $newBill = BillExpert::generate($model, $type, $details);
        //Generate Here

        if ($newBill === false) {
            throw new \yii\web\HttpException(500, Yii::t('app', 'Something was wrong.'));
        }

        if ($newBill->status == 'draft') {
            $this->redirect(['update', 'id' => $newBill->bill_id]);
        } else {
            $this->redirect(['view', 'id' => $newBill->bill_id]);
        }
    }

    /**
     * Permite remover el usuario de la factura
     * @param int $id
     */
    public function actionRemoveCustomer($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->updateAttributes(['customer_id' => null]);

        return [
            'status' => 'success',
        ];

    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionHandwriteDetail($id)
    {

        $model = $this->findModel($id);

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $detail = new \app\modules\sale\models\forms\BillDetailForm();
        $detail->load(Yii::$app->request->post());

        if ($detail->validate()) {

            $billDetail = $model->addDetail([
                'concept' => $detail->concept,
                'unit_net_price' => $detail->unit_net_price,
                'unit_final_price' => $detail->unitFinalPrice,
                'qty' => $detail->qty,
                'unit_id' => $detail->unit_id
            ]);

            return [
                'status' => 'success',
                'detail' => $billDetail
            ];

        } else {

            return [
                'status' => 'error',
                'errors' => \yii\widgets\ActiveForm::validate($detail)
            ];

        }

    }

    /**
     * Permite agregar un producto a la factura
     * @param int $id
     * @param Product $product
     * @param float $qty
     * @return type
     */
    private function addProductDetail($id, $product, $qty = 1)
    {

        $model = $this->findModel($id);

        $detail = $model->addDetail([
            'product_id' => $product->product_id,
            'concept' => $product->name,
            'unit_net_price' => $product->netPrice,
            'unit_final_price' => $product->finalPrice,
            'unit_id' => $product->unit_id,
            'qty' => $qty
        ]);

        return $detail;

    }

    public function actionUpdateQty()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('model_id');
        $model = \app\modules\sale\models\BillDetail::findOne($id);

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->bill->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model != null) {

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'status' => 'success',
                    'model' => $model,
                    'model_id' => $model->bill_detail_id,
                    'extraData' => [
                        'billAmount' => Yii::$app->formatter->asCurrency($model->bill->calculateAmount()),
                        'billTaxes' => Yii::$app->formatter->asCurrency($model->bill->calculateTaxes()),
                        'billTotal' => Yii::$app->formatter->asCurrency($model->bill->calculateTotal()),
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'model' => $model,
                    'model_id' => $model->bill_detail_id,
                    'errors' => $model->getErrors()
                ];
            }

        } else {
            return [
                'status' => 'error',
                'errors' => Yii::t('yii', 'The request page does not exist.')
            ];
        }

    }

    public function actionUpdateLineTotal()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('model_id');
        $model = \app\modules\sale\models\BillDetail::findOne($id);

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $model->bill->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model != null) {
            $model->line_total = (double)Yii::$app->request->post('BillDetail')['line_total'];
            $model->unit_final_price = $model->line_total / $model->qty;
            $model->unit_net_price = (($model->line_total * 100) / (($model->iva * 100) + 100)) / $model->qty;
            $model->line_subtotal = $model->unit_net_price * $model->qty;

            if ($model->updateAttributes(['line_total', 'unit_final_price', 'unit_net_price', 'line_subtotal'])) {
                return [
                    'status' => 'success',
                    'model' => $model,
                    'model_id' => $model->bill_detail_id,
                    'extraData' => [
                        'billAmount' => Yii::$app->formatter->asCurrency($model->bill->calculateAmount()),
                        'billTaxes' => Yii::$app->formatter->asCurrency($model->bill->calculateTaxes()),
                        'billTotal' => Yii::$app->formatter->asCurrency($model->bill->calculateTotal()),
                    ]
                ];
            } else {
                $model->validate();
                return [
                    'status' => 'error',
                    'model' => $model,
                    'model_id' => $model->bill_detail_id,
                    'errors' => $model->getErrors()
                ];
            }

        } else {
            return [
                'status' => 'error',
                'errors' => Yii::t('yii', 'The request page does not exist.')
            ];
        }

    }

    public function actionDeleteDetail($id)
    {

        $model = \app\modules\sale\models\BillDetail::findOne($id);
        $bill = $model->bill;

        //Permisos para actualizar?
        if (!BillExpert::checkAccess('update', $bill->class)) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model != null && $bill->getDeletable()) {

            $model->delete();

        }

        $this->redirect(['update', 'id' => $bill->bill_id]);

    }

    /**
     * Deletes an existing Bill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $return = 'index')
    {

        try {
            $model = $this->findModel($id);

            //Permisos para eliminar?
            if (!BillExpert::checkAccess('delete', $model->class)) {
                throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }

            if ($model->getDeletable()) {
                foreach ($model->getBillDetails()->all() as $bill_detail) {
                    StockMovement::deleteAll(['bill_detail_id' => $bill_detail->bill_detail_id]);
                    $bill_detail->delete();
                }
                $model->delete();
            } else {
                Yii::$app->session->setFlash("error", Yii::t('app', 'The Invoice could not be deleted.'));
            }
        } catch (\Exception $ex) {
            Yii::$app->session->setFlash("error", Yii::t('app', 'The Invoice could not be deleted.') . $ex->getMessage());
        }

        if ($return == 'index') {
            return $this->redirect([$return]);
        } else {
            return $this->redirect(['/checkout/payment/current-account', 'customer' => $model->customer_id]);
        }
    }

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    // selects the PDF generation library based on the APP configuration items.
    
    public function actionPdf($id){
        // gets conf item
        $pdf_company = Config::getConfig('pdf_company')->description;
        
        if($pdf_company == "westnet")
            return $this->WestnetPdf($id);
        
        else if($pdf_company == "bigway")
            return $this->BigwayPdf($id);
        
    }

    // selects the PDF generation library based on the APP configuration items.
    //public function actionPdf($id){
    //    return $this->findModel($id)->makePdf($id);
    //}

    /**
     * Prints the pdf of a single Bill Westnet.
     * @param integer $id
     * @return mixed
     */
    public function WestnetPdf($id)
    {

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

        $model = $this->findModel($id);
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

        $view = $this->render('pdf', [
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

    /**
     * Prints the pdf of a single Bill Bigway.
     * @param integer $id
     * @return mixed
     */
    public function BigwayPdf($id)
    {      
        $model = $this->findModel($id);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $formatter = Yii::$app->formatter;
        $cupon_bill_types = explode(',', \app\modules\config\models\Config::getValue('cupon_bill_types'));
        $is_cupon = (array_search($model->bill_type_id, $cupon_bill_types) !==false);
        $payment = new Payment();
        $payment->customer_id = $model->customer_id;
        $debt = $payment->accountTotal();
        $isConsumidorFinal = false;
        $profile = $model->customer->getCustomerProfiles()->where(['name'=>'Consumidor Final'])->one();
        $company = $model->customer->company;
        $company = (isset($company) ? $company : $model->customer->parentCompany );
        $companyData = $model->company;

        //echo'<pre>'; var_dump( $companyData->name  ); die;

        $cuit = str_replace('-', '', $model->company->tax_identification);
        $code = $cuit . sprintf("%02d", $model->billType->code) . sprintf("%04d", $model->getPointOfSale()->number) . $model->ein . (new \DateTime($model->ein_expiration))->format("Ymd");

        $barcode = new BarcodeGeneratorPNG();

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

        $content = $this->renderPartial('bigway-pdf.php',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'formatter' => $formatter,
            'cupon_bill_types' => $cupon_bill_types,
            'is_cupon' => $is_cupon,
            'payment' => $payment,
            'debt' => $debt,
            'isConsumidorFinal' => $isConsumidorFinal,
            'profile' => $profile,
            'company' => $company,
            'companyData' => $companyData,
            'barcode' => $barcode,
            'code' => $code,
            'qrCode' => $qrCode

        ]);

            
        $pdf = new Pdf([
               
            'mode' => Pdf::MODE_UTF8, 
            
            'format' => Pdf::FORMAT_LEGAL, 
           
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            
            'destination' => Pdf::DEST_BROWSER, 
           
            'content' => $content,  
            'filename' => "documento.pdf",
            'cssFile' => '@app/modules/sale/web/css/sale-bill-pdf.css',
            
            'options' => ['title' => ""],
            
            'methods' => [ 
                'SetTitle' => '',
                'SetFooter'=>['Página {PAGENO} de {nb}'],
            ],
            'marginTop' => 5,
        ]);

            
        return $pdf->render();   
    }

    /**
     * Genera un codigo de barras en formato jph
     * @return type
     */
    public function actionBarcode($id)
    {
        $model = $this->findModel($id);
        $barcode = new Barcode();
        $barcode->setGenbarcodeLocation(Yii::$app->params['genbarcode_location']);
        $barcode->setMode(Barcode::MODE_PNG);
        $cuit = str_replace('-', '', $model->company->tax_identification);
        $code = $cuit . sprintf("%02d", $model->billType->code) . sprintf("%04d", $model->getPointOfSale()->number) . $model->ein . (new \DateTime($model->ein_expiration))->format("Ymd");

        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = \yii\web\Response::FORMAT_RAW;

        return $barcode->outputImage($code);
    }

    /**
     * Envia el comprobante por email al correo del customer
     * @param $id
     */
    public function actionEmail($id, $from = 'all_bills', $email = null)
    {
        $model = $this->findModel($id);

        $pdf = $this->actionPdf($id);
        $pdf = substr($pdf, strrpos($pdf, '%PDF-'));
        $fileName = "/tmp/" . 'Comprobante' . sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number) . "-" . $model->customer_id . ".pdf";
        $file = fopen($fileName, "w+");
        fwrite($file, $pdf);
        fclose($file);

        if (empty($email) && trim($model->customer->email) == "" && trim($model->customer->email2) == "") {
            Yii::$app->session->setFlash("error", Yii::t("app", "The Client don't have email."));
            return $this->redirect(['index']);
        }

        if ($model->sendEmail($fileName, $email)) {
            Yii::$app->session->setFlash("success", Yii::t('app', 'The email is sended succesfully.'));
        } else {
            Yii::$app->session->setFlash("error", Yii::t('app', 'The email could not be sent.'));
        };

        if ($from === 'all_bills') {
            return $this->redirect(['index']);
        } else {
            return $this->redirect(['/checkout/payment/current-account', 'customer' => $model->customer_id]);
        }
    }
	
    /**
     * Envia el comprobante por email al correo del customer
     * @param $id
     */
    public function actionEmailConsole($id, $from = 'all_bills', $email = null)
    {
        $model = $this->findModel($id);

        $pdf = $this->actionPdf($id);
        $pdf = substr($pdf, strrpos($pdf, '%PDF-'));
        $fileName = "/tmp/" . 'Comprobante' . sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number) . "-" . $model->customer_id . ".pdf";
        $file = fopen($fileName, "w+");
        fwrite($file, $pdf);
        fclose($file);


        if ($model->sendEmail($fileName, $email)) {
        	return true;
        } else {
            return false;
        };

    }


    public function actionInvoiceCustomer($customer_id = null)
    {

        $searchModel = new \app\modules\sale\models\search\InvoiceCustomerSearch;

        if ($customer_id) {
            $searchModel->customer_id = $customer_id;
        }

        if ($searchModel->load(Yii::$app->request->post()) && $searchModel->validate()) {

            $period = new \DateTime($searchModel->period);

            $customer = $searchModel->search();
            $billType = $customer->getDefaultBillType();

            $cti = new \app\modules\sale\modules\contract\components\ContractToInvoice();
            $cti->invoice($customer->company, $billType->bill_type_id, $customer->customer_id, $period, $searchModel->includePlan);

            return $this->redirect(['index', 'BillSearch[customer_id]' => $customer->customer_id]);
        }

        return $this->render('invoice', [
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @param $id
     */
    public function actionResend($id)
    {
        $model = $this->findModel($id);

        $model->invoice();
        return $this->redirect(['index']);
    }

    private function getDigitoVerificador($codigo)
    {
        // 1 - Sumo los pares e impares desde la izquierda.
        $impares = 0;
        $pares = 0;
        for ($i = 0; $i < strlen($codigo); $i++) {
            if (($i % 2)) {
                $pares += $codigo[$i];
            } else {
                $impares += $codigo[$i];
            }
        }
        // 2 - multiplicar la suma obtenida en la etapa 1 por el número 3
        $impares = $impares * 3;

        // 3 - Sumo pares e impares
        $digito = 0;
        $suma = $pares + $impares;
        // 4 - buscar el menor número que sumado al resultado obtenido en la etapa 4 dé un
        //      número múltiplo de 10. Este será el valor del dígito verificador del módulo 10.
        while ((floor($suma / 10) * 10) <> $suma) {
            $suma++;
            $digito++;
        }
        if ($digito == 10) {
            $digito = 0;
        }

        return $digito;
    }

    /**
     * @return string
     * Muestra los ultimos comprobantes de cada cliente y los envía por mail.
     */
    public function actionGetLastBills()
    {
        $searchModel = new BillSearch();
        $query = $searchModel->searchLastBills(Yii::$app->request->getQueryParams());
        $dataProvider = new ActiveDataProvider(['query' => $query]);

        if(array_key_exists('submit_send', Yii::$app->request->getQueryParams())){
            $response = Yii::$app->getResponse();
            $response->headers->removeAll();
            $email_success = 0;
            $email_error = 0;

            foreach ($dataProvider->getModels() as $model)
            {
                $pdf = $this->actionPdf($model->bill_id);
                $pdf = substr($pdf, strrpos($pdf, '%PDF-'));
                $fileName = "/tmp/" . 'Comprobante' . sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number) . "-" . $model->customer_id . ".pdf";
                $file = fopen($fileName, "w+");
                fwrite($file, $pdf);
                fclose($file);

                if($model->sendEmail($fileName)){
                    $email_success ++;
                } else {
                    $email_error ++;
                }
            }

            Yii::$app->response->format = Response::FORMAT_HTML;
            $this->layout = '//main';

            Yii::$app->session->setFlash('info', "$email_success ". Yii::t('app', "Emails were sended") ." $email_error " . Yii::t('app', 'Emails failed to send'));
        }

        return $this->render('send_last_bills', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Permite actualizar el CAE y fecha de vencimiento de un comprobante y devuelve la vista a la vista del comprobante
     */
    public function actionUpdateEinAndEinExpiration($bill_id, $ein, $ein_expiration)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($bill_id);

        if($model->updateEinAndEinExpiration($ein, $ein_expiration)) {
            return [
                'status' => 'success',
                'msg' => ''
            ];
        }

        return [
            'status' => 'error',
            'msg' => Yii::t('app', 'Ein and ein expiration cant be updated')
        ];

    }

}
