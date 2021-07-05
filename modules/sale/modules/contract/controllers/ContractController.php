<?php

namespace app\modules\sale\modules\contract\controllers;

use app\components\web\Controller;
use app\modules\automaticdebit\models\AutomaticDebit;
use app\modules\config\models\Config;
use app\modules\sale\models\Address;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\models\search\FundingPlanSearch;
use app\modules\sale\modules\contract\components\ContractLowService;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\search\ContractDetailSearch;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\EmptyAds;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\models\Vendor;
use DateTime;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ContractController implements the CRUD actions for Contract model.
 */
class ContractController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all Contract models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ContractSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Contract models from current user.
     * @return mixed
     */
    public function actionVendorList() {
        $searchModel = new ContractSearch();
        $searchModel->setScenario('vendor-search');

        $vendor = Vendor::findByUserId(Yii::$app->user->id);

        if (empty($vendor)) {
            throw new HttpException(404, Yii::t('app', 'Are you a vendor?'));
        }

        $searchModel->vendor_id = $vendor->vendor_id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('vendor-list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'vendor' => $vendor
        ]);
    }

    /**
     * Displays a single Contract model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model= $this->findModel($id);
        $products = ArrayHelper::map(Product::find()->andWhere(['type' => 'product'])->andWhere(['LIKE', 'name', 'Recargo por Extensión de Pago'])->all(), 'product_id', 'name');

        $vendors = ArrayHelper::map(Vendor::find()->leftJoin('user', 'user.id=vendor.user_id')
            ->andWhere(['OR',['IS', 'user.status', null], ['user.status' => 1]])
            ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])
            ->all(), 'vendor_id', 'fullName');

        if($model->canView()){
            return $this->render('view', [
                'model' => $model,
                'products' => $products,
                'vendors' => $vendors
            ]);
        }else{
            throw new ForbiddenHttpException(\Yii::t('app', 'You can`t do this action'));
        }    
    }

    /**
     * Creates a new Contract model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id = null) {
        $model = new Contract();
        $customer = Customer::findOne($customer_id);
        $address = new Address();

        //En caso en que vaya a guardar los datos, antes de verificar si puedo comenzar
        //El proceso de guardado, cargo los modelos por si  acaso deba de volver a la vista create
        //Cuando se carga el formulario por primera vez salta la carga de modelos
        if (empty($_POST)) {

            $address = $customer->address;
        }else{
            $same_address = Yii::$app->request->post('same_address');

            if ($same_address) {
                $model->address_id = $customer->address_id;
                $address = $customer->address;
            } else {
                $address->load(Yii::$app->request->post());
            }
        }

        $model->customer_id = $customer->customer_id;
        $contractDetailPlan = new ContractDetail();
        $contractDetailIns= new ContractDetail();
        //Plan por defecto para vendedores:
        if (Yii::$app->user->identity->hasRole('seller', false)) {
            $defaultPlan = Product::find()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'default-seller-plan'])
                    ->one();

            if ($defaultPlan) {
                $contractDetailPlan->product_id = $defaultPlan->product_id;
            }
        }
        if(!empty($_POST)){
            if ((User::hasPermission('user-can-select-vendor') || $contractDetailPlan->vendor_id === null) && 
                    !empty(Yii::$app->request->post('Contract')['vendor_id'])) {
                $model->vendor_id = Yii::$app->request->post('Contract')['vendor_id'];
                $contractDetailPlan->vendor_id = Yii::$app->request->post('Contract')['vendor_id'];
                $contractDetailIns->vendor_id = Yii::$app->request->post('Contract')['vendor_id'];
            } else {
                //$contractDetail asigna vendedor en init()
                $vendor = Vendor::findByUserId(Yii::$app->user->id);
                $model->vendor_id = $vendor ? $vendor->vendor_id : NULL;
            }
            if(Yii::$app->request->post()['Contract']['instalation_schedule'] !== '') {
                $model->instalation_schedule= Yii::$app->request->post()['Contract']['instalation_schedule'];
            }
        }
        if(!empty($_POST['same_address'])){
            $same_address = $_POST['same_address'];
        }else{
            $same_address= false;
        }

        if (!empty($_POST) && $contractDetailPlan->load(Yii::$app->request->post()) 
                && (isset(Yii::$app->request->post()['contractDetailIns'] ) && (isset(Yii::$app->request->post()['contractDetailIns']['product_id']) 
                        && isset(Yii::$app->request->post()['contractDetailIns']['count']) && isset(Yii::$app->request->post()['contractDetailIns']['funding_plan_id'])))) {
            
            $contractDetailIns->product_id= Yii::$app->request->post()['contractDetailIns']['product_id'];
            $contractDetailIns->count= Yii::$app->request->post()['contractDetailIns']['count'];
            $contractDetailIns->funding_plan_id= Yii::$app->request->post()['contractDetailIns']['funding_plan_id'];
            $contractDetailIns->discount_id= Yii::$app->request->post()['contractDetailIns']['discount_id'];
            
            $transaction = Yii::$app->db->beginTransaction();
            try {

                $model->date = (new \DateTime('now'))->format('d-m-Y');
                
                if ($model->validate()) {
                    
                    if (!$same_address) {
                        if (!$address->validate()) {
                            throw new \Exception(Yii::t('app', 'Address not valid.'));
                        }
                        $address->save();
                        $model->address_id = $address->address_id;
                    }

                    $model->save();
                    $contractDetailPlan->load(Yii::$app->request->post());
                    $contractDetailPlan->contract_id = $model->contract_id;
                    $contractDetailIns->contract_id= $model->contract_id;
                    if ($contractDetailPlan->validate() && $contractDetailIns->validate()) {
                        $contractDetailPlan = $model->addContractDetail([
                            'contract_id' => $model->contract_id,
                            'product_id' => $contractDetailPlan->product_id,
                            'date' => (new \DateTime('now'))->format('Y-m-d'),
                            'discount_id' => $contractDetailPlan->discount_id,
                            'vendor_id' => $contractDetailPlan->vendor_id
                        ]);
                        
                        $contractDetailIns = $model->addContractDetail([
                            'contract_id' => $model->contract_id,
                            'product_id' => $contractDetailIns->product_id,
                            'date' => (new \DateTime('now'))->format('Y-m-d'),
                            'from_date' => (new \DateTime('now'))->format('Y-m-d'),
                            'discount_id' => $contractDetailIns->discount_id,
                            'funding_plan_id' => ($contractDetailIns->funding_plan_id == 0 ? null : $contractDetailIns->funding_plan_id ),
                            'vendor_id' => $contractDetailIns->vendor_id]);
                    } else {
                        throw new \Exception('Contract detail cannot be saved. Some data is wrong or missing.');
                    }
                } else {
                    throw new \Exception('Contract cannot be saved. Some data is wrong or missing.');
                }

                $transaction->commit();

                if ($model->hasMethod('createMesaTicket')) {
                    $config = Config::getConfig('disabled_communication_mesa');

                    if(isset($config) && !$config->item->description){
                    //Crea el ticket en mesa ver configuración de behaviors en modelo Contract
                        $model->createMesaTicket($model);
                        
                    }

                }


                if(Yii::$app->request->post('mode') === '1'){
                    return $this->redirect(['/sale/contract/contract/update', 'id' => $model->contract_id]);
                }else{
                    return $this->redirect(['/sale/contract/contract/view', 'id' => $model->contract_id]);
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                $model->isNewRecord = true;
                Yii::info($ex);
                Yii::$app->session->addFlash('error', $ex->getMessage());
            }
        }            
        if (!empty($_POST)) {
            
            if (empty(Yii::$app->request->post()['contractDetailIns']['product_id'])) {
                $contractDetailIns->addError('product_id');
            }
            
            if (empty(Yii::$app->request->post()['contractDetailIns']['count'])) {
                $contractDetailIns->addError('count');                
            }
            
           if (empty(Yii::$app->request->post()['contractDetailIns']['funding_plan_id'])){
               $contractDetailIns->addError('funding_plan_id');
           }
           if($contractDetailIns->hasErrors()){
               \Yii::$app->session->addFlash('error', Yii::t('app', 'You most complete all data'));
           }
        }

        if (Yii::$app->user->identity->hasRole('seller', false)) {
            $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'seller-plan'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');

        }else if(Yii::$app->user->identity->hasRole('internal-seller', false)){
                  $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'plan-para-vendedores-internos'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');

        }else if(Yii::$app->user->identity->hasRole('external-seller', false)){
                  $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'plan-para-vendedores-externos'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');    

        } else {
            $subQueryplans = Product::find()
                    ->where(['type'=>'plan', 'status' => 'enabled' ])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->distinct()
                    ->orderBy('product.name');
        }
        
        if ($customer->customerCategory->name === 'Familia') {
            $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-residencial'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();
            
        }elseif($customer->customerCategory->name === 'Empresa'){
             $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-empresa'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();
        }else{
            $plans= $subQueryplans->all();
        }
        $instalationProd= $model->getInstalationCharges();
        
        if (count($instalationProd) === 1) {
            $contractDetailIns->product_id = $instalationProd[0]->product_id;;
        }

        $vendors = Vendor::find()->leftJoin('user', 'user.id=vendor.user_id')
            ->andWhere(['OR',['IS', 'user.status', null], ['user.status' => 1]])
            ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        return $this->render('create', [
                    'same_address' => $same_address,
                    'model' => $model,
                    'customer' => $customer,
                    'address' => $address,
                    'contractDetailPlan' => $contractDetailPlan,
                    'contractDetailIns' => $contractDetailIns,
                    'plans' => $plans,
                    'instalationProd' => $instalationProd,
                    'vendors' => $vendors
        ]);
    }

    /**
     * Updates an existing Contract model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if($model->canUpdate()){
            $same_address = Yii::$app->request->post('same_address');
            $customer = Customer::findOne($model->customer_id);
            $address = Address::findOne($model->address_id);
            $contractDetailPlan = $model->getContractDetailsByType('plan')->one();

        if ((Yii::$app->request->isPost && count($model->contractDetails) > 1 && User::hasRole('seller')) ||
                (Yii::$app->request->isPost &&  (User::hasRole('seller-office') || (User::hasPermission('update-contract') && !User::hasRole('seller')))) ) {

                try {
                    // Busco el plan
                    /* $model->to_date = null;
                      $model->from_date = null;
                      $model->save(); */
                    // Clono para comparar
                    $contractDetailOld = clone($contractDetailPlan);
                    if ($contractDetailPlan->load(Yii::$app->request->post())) {
                        // Si no son iguales, actualizo la
                        if (!$contractDetailPlan->isEqual($contractDetailOld)) {
                            // Si no es borrador, registro historial
                            $toUpdate = ['from_date', 'status', 'product_id'];
                            if ($contractDetailPlan->status != Contract::STATUS_DRAFT) {
                                if (!empty($contractDetailPlan->from_date)) {
                                    //$contractDetailOld->to_date

                                    try {
                                        $fromDate = (new DateTime($contractDetailPlan->from_date));
                                    } catch (\Exception $ex) {
                                        $fromDate = (new DateTime($contractDetailPlan->date));
                                    }
                                    try {
                                        $oldFrom = (new DateTime($contractDetailOld->from_date));
                                    } catch (\Exception $ex) {
                                        $oldFrom = (new DateTime($contractDetailOld->date));
                                    }

                                    if ($fromDate > $oldFrom) {
                                        //Creo un registro en Programmed plan changed

                                        // Le pongo la fecha de fin al plan anterior para que la guarde en el log.
                                        $contractDetailOld->to_date = (new DateTime($contractDetailPlan->from_date))->modify('-1 day')->format('d-m-Y');
                                        $contractDetailOld->createLog();
                                        $customerLog= new CustomerLog();
                                        $customerLog->createUpdateLog($model->customer_id, 'Plan', $contractDetailOld->product->name, $contractDetailPlan->product->name, 'Contract', $model->contract_id);
                                    } else {
                                        $contractDetailPlan->addError('from_date', Yii::t('app', 'The new date cannot be greater than the old date.'));
                                        throw new \Exception(Yii::t('app', 'The new date cannot be greater than the old date.'));
                                    }
                                } else {
                                    $contractDetailPlan->addError('from_date', Yii::t('app', 'The date cant be empty.'));
                                    throw new \Exception(Yii::t('app', 'The date cant be empty.'));
                                }
                                if($contractDetailPlan->product_id != $contractDetailOld->product_id) {
                                    $contractDetailPlan->applied = false;
                                    $toUpdate[] = 'applied';
                                }
                            }

                            $contractDetailPlan->save(false, $toUpdate);
                        }
                    }

                    // Si es la misma
                    if ($same_address) {
                        if ($customer->address_id != $address->address_id) {
                            $model->address_id = $customer->address_id;
                            $address->delete();
                        }
                    } else { // Guardo los datos del domicilio.
                        $address = new Address();
                        if ($address->load(Yii::$app->request->post()) && $address->save()) {
                             $address->refresh();
                             $model->setAttribute('address_id', $address->address_id);
                        }
                    }

                    $model->address_id = $address->address_id;
                    $model->update(false);

                    //Si es necesario modificar el vendedor asignado:
                    if (User::hasPermission('user-can-select-vendor') && !empty(Yii::$app->request->post('Contract')['vendor_id'])) {
                        //Si el vendedor de los items es el mismo que el del contrato, tmb los actualizamos
                        ContractDetail::updateAll(['vendor_id' => Yii::$app->request->post('Contract')['vendor_id']], [
                            'vendor_id' => $model->vendor_id,
                            'contract_id' => $model->contract_id
                        ]);

                        //Actualizamos el vendedor del contrato
                        $model->vendor_id = Yii::$app->request->post('Contract')['vendor_id'];
                        $model->update(false);
                    }

                    if(Yii::$app->request->post()['Contract']['instalation_schedule'] !== '') {
                        $model->instalation_schedule= Yii::$app->request->post()['Contract']['instalation_schedule'];
                    }

                    $model->address_id = $address->address_id;

                    $model->update(false);

                    // Nos aseguramos que si el contrato esta en estado activo, el cliente quede en estado habilitado
                    // para evitar errores, especialmente con la facturacion. Usamos el metodo save() para que el cambio quede en el historial
                    if ($model->status === Contract::STATUS_ACTIVE && $model->customer->status !== Customer::STATUS_ENABLED) {
                        $model->customer->status = Customer::STATUS_ENABLED;
                        $model->customer->save();
                    }


                    return $this->redirect(['/sale/contract/contract/view',
                                'id' => $model->contract_id]);
                } catch (\Exception $ex) {
                    Yii::$app->session->setFlash('error', $ex->getMessage());
                }
            } else {
                if(!$model->address_id) {
                    $model->address_id = $customer->address_id;
                    $model->update(false);
                    $address = $model->address;
                } else {
                    $same_address = ($customer->address_id == $model->address_id);
                }
                if (count($model->contractDetails) <= 1 && Yii::$app->request->isPost ) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'You must add at least one contract detail'));                
                }
            }

            if (Yii::$app->user->identity->hasRole('seller', false)) {
            $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'seller-plan'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');
            }else if(Yii::$app->user->identity->hasRole('internal-seller', false)){
                $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'plan-para-vendedores-internos'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');
  
            }else if(Yii::$app->user->identity->hasRole('external-seller', false)){
                        $subQueryplans = Product::find()
                        ->distinct()
                        ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                        ->joinWith('categories')
                        ->andWhere(['category.system' => 'plan-para-vendedores-externos'])
                        ->andWhere(['or',
                            ['company_id'=>$customer->parent_company_id],
                            ['company_id'=>null]
                        ])
                        ->orderBy('product.name');           
            } else {
                $subQueryplans = Product::find()
                        ->where(['type'=>'plan', 'status' => 'enabled' ])
                        ->andWhere(['or',
                            ['company_id'=>$customer->parent_company_id],
                            ['company_id'=>null]
                        ])
                        ->distinct()
                        ->orderBy('product.name');
                        
            }
        if ($customer->customerCategory->name === 'Familia') {
            $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-residencial'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();  
            
        }elseif($customer->customerCategory->name === 'Empresa'){
             $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-empresa'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();  
        }else{
            $plans= $subQueryplans->all();
        }
        
        $instalationProd= $model->getInstalationCharges();
        $vendors = Vendor::find()->leftJoin('user', 'user.id=vendor.user_id')
            ->andWhere(['OR',['IS', 'user.status', null], ['user.status' => 1]])
            ->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
        return $this->render('update', [
                        'model' => $model,
                        'customer' => $customer,
                        'address' => $address,
                        'contractDetailPlan' => $contractDetailPlan,
                        'same_address' => $same_address,
                        'plans' => $plans,
                        'vendors' => $vendors
                        
            ]);
        }else{
            throw new ForbiddenHttpException(\Yii::t('app', 'You can`t do this action'));
        }
    }

    /**
     * Deletes an existing Contract model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Contract model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contract the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Contract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionShowAdditionals($contract_id = null, $contract_detail_id = null) {
        if ($contract_id) {
            $model = $this->findModel($contract_id);

            $customer = $model->customer;
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getContractDetailsByType(null, ['plan'])
            ]);

            if ($contract_detail_id !== null) {
                $model = ContractDetail::findOne(['contract_detail_id' => $contract_detail_id]);
            } else {
                $model = new ContractDetail();
                $model->contract_id = $contract_id;
            }

            return $this->renderAjax('_form-additionals', [
                        'dataProvider' => $dataProvider,
                        'model' => $model,
                        'customer' => $customer
            ]);
        } else {
            $this->layout = '//embed';
            return $this->renderContent('');
        }
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddContractDetail($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $contractDetail = new ContractDetail();
        $contract_detail_id = Yii::$app->request->post('ContractDetail')['contract_detail_id'];
        $product_id_activated_automaticaly = Config::getValue('id-product_id-extension-de-pago');

        // Si estoy actualizando el item, busco el anterior para ver si tengo que crear log.
        // Solo se crea el log en caso de que el estado no sea draft.
        if ($contract_detail_id) {
            $oldContractDetail = ContractDetail::findOne([$contract_detail_id]);
            $contractDetail = ContractDetail::findOne([$contract_detail_id]); // = $oldContractDetail;
            $contractDetail->load(Yii::$app->request->post());

            $contractDetail->discount_id = ($contractDetail->tmp_discount_id ? $contractDetail->tmp_discount_id : null);
            if ($contractDetail->status === Contract::STATUS_DRAFT) {
                $contractDetail->to_date= null;
            }
            $contractDetail->createLog($oldContractDetail);
            if ($contractDetail->funding_plan_id == 0) {
                $contractDetail->funding_plan_id = null;
            }
            if($contractDetail->validate()){
                //Si el producto es "Extension de pago", este debe agregarse en estado activo directamente
                if($product_id_activated_automaticaly == $contractDetail->product_id) {
                    $contractDetail->status = 'active';
                }

                $contractDetail->update(false);
                return [
                        'status' => 'success',
                        'detail' => $contractDetail,
                    ];
            }else{
                return [
                        'status' => 'error',
                        'errors' => ActiveForm::validate($contractDetail)
                    ];
            }
        } else {
            //if(User::hasRole('seller') || Yii::$app->request->post()['ContractDetail']['vendor_id']){
                $contractDetail->load(Yii::$app->request->post());
                $contractDetail->discount_id = ($contractDetail->tmp_discount_id ? $contractDetail->tmp_discount_id : null);
                if ($contractDetail->validate()) {
                    $model = $this->findModel($id);
                    $status = 'draft';

                    //Si el producto es "Extension de pago", este debe agregarse en estado activo directamente
                    if($product_id_activated_automaticaly == $contractDetail->product_id) {
                        $status = 'active';
                    }

                    $contractDetail = $model->addContractDetail([
                        'contract_id' => $id,
                        'customer_id' => $model->customer_id,
                        'product_id' => $contractDetail->product_id,
                        'funding_plan_id' => (!$contractDetail->funding_plan_id ? null : $contractDetail->funding_plan_id),
                        'date' => $model->date,
                        'to_date' => $contractDetail->to_date,
                        'from_date' => $contractDetail->from_date,
                        'discount_id' => $contractDetail->tmp_discount_id,
                        'count' => $contractDetail->count,
                        'vendor_id' => $contractDetail->vendor_id,
                        'status' => $status
                    ]);

                    return [
                        'status' => 'success',
                        'detail' => $contractDetail,
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'errors' => ActiveForm::validate($contractDetail)
                    ];
                }
        }
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionRemoveContractDetail() {
        $status = 'error';
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            ContractDetail::findOne($id)->delete();
            $status = 'ok';
        }
        return [
            'status' => $status
        ];
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionChangeStatusContractDetail() {
        $status = 'error';
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $status = Yii::$app->request->post('to-status');
            $oldContractDetail = ContractDetail::findOne($id);
            $contractDetail = ContractDetail::findOne($id);
            $contractDetail->status = 'canceled';
            $contractDetail->createLog($oldContractDetail);
            $contractDetail->update(['status']);
            foreach (ProductToInvoice::findAll(['contract_detail_id'=> $contractDetail->contract_detail_id]) as $p){
                $p->status= 'canceled';
                $p->update(['status']);
            }
            $status = 'ok';
        }
        return [
            'status' => $status
        ];
    }

    /**
     * Muestra el historial de el contrato y de cada uno de los detalles
     * @param integer $id
     * @return mixed
     */
    public function actionHistory($id) {
        $model = $this->findModel($id);

        $dataContractLogs = new ActiveDataProvider([
            'query' => $model->getContractLogs()
                    ->orderBy(['contract_log_id' => SORT_DESC])
        ]);

        $search = new ContractDetailSearch();

        $dataContractDetailLogs = new ActiveDataProvider([
            'query' => $search->searchLogs([ 'contract_id' => $id])
        ]);

        return $this->render('history', [
                    'model' => $model,
                    'dataContractLogs' => $dataContractLogs,
                    'dataContractDetailLogs' => $dataContractDetailLogs
        ]);
    }

    /**
     *
     */
    public function actionFundingPlans() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null && $parents[0] && $parents[1]) {
                $product_id = $parents[0];
                $count= $parents[1];
                $search = new FundingPlanSearch();
                $out = $search->searchByProduct($product_id, $count);
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Activa contrato
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionActiveContract($id) {
        $model = $this->findModel($id);
        $connection = Connection::findOne(['contract_id' => $model->contract_id]);
        
        if (!$connection) {
            $connection = new Connection();
        }

        $nodesQuery = Node::find();
        $nodesQuery->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name'])
            ->leftJoin('server s', 'node.server_id = s.server_id')
            ->orderBy('node.name');
            

        $nodes = ArrayHelper::map($nodesQuery->all(), 'node_id', 'name');
        
        if (!empty($_POST['Contract']) && $_POST['Contract']['customerCodeADS'] !== '') {
            $code = $_POST['Contract']['customerCodeADS'];
            $customer = Customer::findOne(['customer_id' => $model->customer_id]);
            // Busco el ADS vacio
            $emptyAds = EmptyAds::findOne(['code' => $code , 'used' => false]);
            // Si tiene ADS vacio, tengo que forzar la actualizacion del company en el cliente.
            if(!empty($emptyAds)) {
                $customer->code = $code;
                $customer->payment_code = $emptyAds->payment_code;
                $customer->company_id = $emptyAds->company_id;
                $customer->status = Customer::STATUS_ENABLED;
                $emptyAds->used = true;
                $customer->updateAttributes(['code', 'payment_code', 'company_id', 'status']);
                $emptyAds->updateAttributes(['used']);
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'This ADS has been used before or not exist'));
                return $this->render('active-contract', [
                    'model' => $model,
                    'connection' => $connection,
                    'action' => 'active',
                    'nodes' => $nodes
                ]);
            }
        }

        $model->from_date = Yii::$app->formatter->asDate(new DateTime());
        $model->setScenario('invoice');
        if ($model->load(Yii::$app->request->post()) && $connection->load(Yii::$app->request->post()) && $model->validate()) {
            $connection->contract_id = $model->contract_id;
            $connection->due_date = null;

            // Si viene desde un vendedor, no va a tener empresa, por lo que hay que sacarla de del nodo.
            //Si la coneccion se guarda
            if ($connection->save() && $model->save()) {
                $cti = new ContractToInvoice();
                if ($cti->createContract($model, $connection)) {
                    $model->customer->sendMobileAppLinkSMSMessage();
                    Ticket::createGestionADSTicket($model->customer_id);
                    $model->customer->updateAttributes(['status' => Customer::STATUS_ENABLED]);
                    return $this->redirect(['/sale/contract/contract/view', 'id' => $model->contract_id]);
                }
            }
        }

        return $this->render('active-contract', [
                    'model' => $model,
                    'connection' => $connection,
                    'action' => 'active',
                    'nodes' => $nodes
        ]);
    }

    /**
     * Activa contrato
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateConnection($id) {
        $model = $this->findModel($id);
        $connection = Connection::findOne(['contract_id' => $model->contract_id]);
        if (!$connection) {
            $connection = new Connection();
        }

        if ($connection->load(Yii::$app->request->post()) && $connection->load(Yii::$app->request->post())) {
            $connection->due_date = null;
            if ($connection->validate()) {
                $connection->save();
                return $this->redirect(['/sale/contract/contract/view', 'id' => $model->contract_id]);
            }
        }

        return $this->render('update-connection', [
                    'model' => $model,
                    'connection' => $connection
        ]);
    }

    /**
     * Cancela un contrato vigente.
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCancelContract($id) {
        
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format= Response::FORMAT_JSON;
            $model = $this->findModel($id);

            //$connection = Connection::findOne(['contract_id' => $model->contract_id]);
            $model->setScenario('cancel');
            $model->to_date = (new DateTime())->format('d-m-Y');
            if ($model->save(true)) {               
                
                $cti = new ContractToInvoice();
                
                if ($cti->cancelContract($model)) {
                    //$connection= Connection::findOne(['contract_id' => $model->contract_id]);
                    //$connection->status= Connection::STATUS_DISABLED;
                    //$connection->update(false);
                    if(isset($model->customer->automaticDebit)){
                       $model->customer->automaticDebit->status = AutomaticDebit::DISABLED_STATUS;
                       $model->customer->automaticDebit->save();
                    }
                    $model->customer->updateAttributes(['status' => Customer::STATUS_DISABLED]);

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Contract canceled successful'));
                    return ['status' => 'success'];
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Can\`t cancel this contract'));
                    return ['status' => 'error'];
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'Can\`t cancel this contract'));
                return ['status' => 'error'];
            }
           
        }else{
            throw new ForbiddenHttpException();
        }
    }

    /**
     * Retorno la conexion dado el id
     * @param $id
     * @return null|Connection
     * @throws NotFoundHttpException
     */
    private function getConnection($id) {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Cambia la empresa de la conexion
     * @param $connection_id
     * @param $company_id
     */
    public function actionChangeCompany($connection_id, $company_id) {
        Yii::$app->response->format = 'json';

        $response = [];

        $connection = $this->getConnection($connection_id);
        if ($connection->payment_code == null ||
                ( $connection->company_id == null && $connection->node->company_id != $company_id ) ||
                ( $connection->company_id != null && $connection->company_id != $company_id )
        ) {
            $connection->company_id = $company_id;
            $customer = $connection->contract->customer;
            $customer->company_id = $company_id;
            try {
                Yii::$app->formatter->asDate($connection->due_date, 'yyyy-MM-dd');
            } catch (\Exception $ex) {
                $connection->due_date = null;
            }
            if ($connection->save() && $customer->save()) {
                $response = [
                    'status' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Yii::t('westnet', 'Can\'t change the Company.')
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => Yii::t('westnet', 'The Company is already assigned.')
            ];
        }

        return $response;
    }

    /**
     * @param $connection_id
     * @param $node_id
     */
    public function actionChangeNode($connection_id, $node_id, $ap_id = null) {
        Yii::$app->response->format = 'json';

        $response = [];

        $connection = $this->getConnection($connection_id);
        if ($connection->node_id != $node_id && (empty($ap_id) || $connection->access_point_id != $ap_id)) {
            $node = Node::findOne(['node_id'=>$node_id]);
            $connection->old_server_id = $connection->server_id;
            $connection->server_id = $node->server_id;
            $connection->node_id = $node_id;
            $connection->access_point_id = $ap_id;
            try {
                Yii::$app->formatter->asDate($connection->due_date, 'yyyy-MM-dd');
            } catch (\Exception $ex) {
                $connection->due_date = null;
            }
            $connection->updateIp();
            if ($connection->save()) {
                $response = [
                    'status' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Yii::t('westnet', 'Can\'t change the Node.')
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => Yii::t('westnet', 'The Node is already assigned.')
            ];
        }

        return $response;
    }

    public function actionActiveNewItems($contract_id) {
        Yii::$app->response->format = 'json';
        $model = $this->findModel($contract_id);
        $cti = new ContractToInvoice();
        if ($cti->updateContract($model)) {
            Yii::$app->session->addFlash('success', Yii::t('app', 'Items activated correctly.'));
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'fail',
                'message' => Yii::t('app', 'Can\'t activate the items.')
            ];
        }
    }

    /**
     * @param $customer_id
     */
    public function actionListContracts() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null && $parents[0]) {
                $customer_id = $parents[0];
                $search = new ContractSearch();
                $out = $search->findByCustomerForSelect($customer_id);
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
    
    
    /**
     * Comienza el proceso de baja de un contrato activo
     * @param type $contract_id
     */
    public function actionLowProcessContract($contract_id){
        
        $contract = $this->findModel($contract_id);


        $service = new ContractLowService();
        try {
            $date = new \DateTime(Yii::$app->request->post('date'));
            $category_id = Yii::$app->request->post('category_id');
            $credit = Yii::$app->request->post('credit');

            if($service->startLowProcess($contract, $date, $category_id, $credit)){
                Yii::$app->session->addFlash('success', Yii::t('app','Low proccess begin successfull'));
            }

        } catch(\Exception $ex) {
            error_log($ex->getTraceAsString());
            error_log($ex->getLine());
            error_log($ex->getMessage());
            error_log($ex->getFile());
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(['view', 'id' => $contract_id]);
    }

    /**
     * @param $contract_id
     * @return Response
     */
    public function actionActiveContractAgain($contract_id){
        
        $contract= $this->findModel($contract_id);
        
        if ($contract->status === Contract::STATUS_LOW_PROCESS){
            $transaction= Yii::$app->db->beginTransaction();
            $contract->updateAttributes(['status' => Contract::STATUS_ACTIVE]);
            $connection= Connection::findOne(['contract_id' => $contract->contract_id]);
            $connection->updateAttributes(['status' => Connection::STATUS_ENABLED]);
            $contract->customer->updateAttributes(['status' => Customer::STATUS_ENABLED]);
            $transaction->commit();
            return $this->redirect(['view', 'id' => $contract_id]);
        }else{
           Yii::$app->session->setFlash('error', Yii::t('app', "Can't active  this contract again. The contract status must be ".Contract::STATUS_LOW_PROCESS));
           return $this->redirect(['view', 'id' => $contract_id]);
        }   
    }


    public function actionChangeIp($id) {
        Yii::$app->response->format = 'json';

        $response = [];

        $connection = $this->getConnection($id);
        if ($connection->node_id) {
            $connection->updateIp();
            if ($connection->save()) {
                $response = [
                    'status' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Yii::t('westnet', 'Can\'t change the IP.')
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => Yii::t('westnet', 'The Node is not selected.')
            ];
        }

        return $response;
    }

    
    /**
     * Setea el nodo tentativo a un grupo de contratos
     * @return type
     */
    public function actionSetTentativeNode(){
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format= Response::FORMAT_JSON;
            $idsContracts= Yii::$app->request->post()['contracts'];
            $node = Yii::$app->request->post('tentative_node');
            
            foreach ($idsContracts as $id) {
                $contract= $this->findModel($id);
                if(!$contract->setTentativeNode($node)){
                    return ['status' => 'error', 'message' => Yii::t('app', 'Can`t set tentative node to any contract')];
                }
            }
            
            return ['status' => 'success'];
        }
    }
    
    public function actionRejectedService($id, $type){
        $model= $this->findModel($id);
        
        if ($type === 'no-want') {
            $model->status = Contract::STATUS_NO_WANT;
        }else{
            $model->status = Contract::STATUS_NEGATIVE_SURVEY;
        }
        
        $model->updateAttributes(['status']);
        
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionRevertNegativeSurvey($contract_id)
    {
        $model = Contract::findOne($contract_id);
        $model->revertNegativeSurvey();
        return $this->redirect(['view', 'id' => $contract_id]);
    }


    /**
     * Devuelve un array con contratos para ser listados en un select2
     */
    public function actionGetContractsByCustomer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];
        $pre_selected_contract_id = null;

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $customer_id = $parents[0];

                if(isset($_POST['depdrop_params'])) {
                    $pre_selected_contract_id = $_POST['depdrop_params'][0];
                }
                $customer = Customer::findOne($customer_id);

                if(!$customer) {
                    throw new BadRequestHttpException('Customer not found');
                }

                $selected = 0;
                $contracts = $customer->getContracts()->andWhere(['status' => Contract::STATUS_ACTIVE])->all();
                foreach ($contracts as $contract) {
                    $out[] = ['id' => $contract->contract_id, 'name' => "[$contract->contract_id] Contracto en " .$contract->address->shortAddress];
                }

                return ['output' => $out, 'selected'=> $pre_selected_contract_id ? $pre_selected_contract_id : $selected];
            }
        }
    }

    public function actionUpdateOnIsp($contract_id)
    {
        $contract = $this->findModel($contract_id);

        if ($contract->updateOnISP()) {
            Yii::$app->session->addFlash('success', Yii::t('app','Contract updated on ISP successfull'));
            return $this->redirect(['view', 'id' => $contract->contract_id]);
        }

        Yii::$app->session->addFlash('error', Yii::t('app','Errors occurred at update contract on ISP'));
        return $this->redirect(['view', 'id' => $contract->contract_id]);
    }

    /**
     * Retorna todos los descuentos por producto y si aplican a producto o customer
     */
    public function actionApByNode()
    {
        $out = [];
        $params = Yii::$app->request->post('depdrop_parents', null);
        $node_id  = $params[0];
        
        if($params) {
            if($node_id) {
                $query = Node::findOne($node_id)
                    ->getAccessPoints()
                    ->select(['access_point_id as id', 'name']);
                
                $out = $query->asArray()->all();
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }

        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }
}
