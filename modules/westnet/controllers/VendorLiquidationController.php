<?php

namespace app\modules\westnet\controllers;

use app\modules\westnet\components\VendorLiquidationService;
use app\modules\westnet\models\Vendor;
use Yii;
use app\modules\westnet\models\VendorLiquidation;
use app\modules\westnet\models\VendorLiquidationProcess;
use app\modules\westnet\models\ProductCommission;
use app\modules\westnet\models\search\VendorLiquidationSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\models\BatchLiquidationModel;
use yii\data\ActiveDataProvider;
use app\components\helpers\EmptyLogger;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\models\Product;
use yii\data\ArrayDataProvider;
use app\modules\westnet\models\VendorLiquidationItem;
use app\modules\checkout\models\search\PaymentSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/**
 * VendorLiquidationController implements the CRUD actions for VendorLiquidation model.
 */
class VendorLiquidationController extends Controller
{
    /**
     * Lists all VendorLiquidation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VendorLiquidationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VendorLiquidation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $query = $model->getVendorLiquidationItems();

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        
        return $this->render('view', [
            'model' => $model,
            'itemsDataProvider' => $itemsDataProvider
        ]);
    }

    /**
     * Creates a new VendorLiquidation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        set_time_limit(600); // 10 minutes of wait time for response
        $model = new VendorLiquidation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            $year = Yii::$app->formatter->asDate($model->period, 'yyyy');
            $month = Yii::$app->formatter->asDate($model->period, 'MM');

            //Detalles del mes correspondiente al periodo
            $query = ContractDetail::find()->andWhere("MONTH(date)<=$month AND YEAR(date)<=$year");
            $query->join('natural left join', 'vendor_liquidation_item')->where('vendor_liquidation_item.vendor_liquidation_id IS NULL');
            $query->andWhere(['vendor_id' => $model->vendor_id]);

            $details = $query->all();

            if($details){
                $this->liquidateVendorItems($model, $details);
            }
            
            return $this->redirect(['view', 'id' => $model->vendor_liquidation_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing VendorLiquidation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vendor_liquidation_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing VendorLiquidation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(Url::to(['index'])); 
    }
    
    /**
     * Liquidacion por lotes
     * @return mixed
     */
    public function actionBatch()
    {
        Yii::setLogger(new EmptyLogger());
        set_time_limit(0);

        $model = new BatchLiquidationModel();


        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Query para buscar vendedores
            $query = $model->findVendors();
            
            //Query para buscar detalles de contrato
            $contractDetailsQuery = $model->findContractsDetails();
            
            $transaction = Yii::$app->db->beginTransaction();
            
            try{
                //Por cada vendedor
                foreach($query->batch() as $vendors){
                    //var_dump(count($vendors)." vendors");
                    //var_dump(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]."s batchin'\n");
                    foreach ($vendors as $vendor) {
                        //var_dump("vendor ". $vendor->name);
                        //$start = microtime(true);
                        $liq = VendorLiquidation::create($vendor, $model->period);
                        //var_dump(microtime(true) - $start."s to create model vl\n");

                        //Clonamos la query para agregar vendor_id a la condicion de busqueda
                        $cdq = clone($contractDetailsQuery);
                        $cdq->andWhere(['vendor_id' => $vendor->vendor_id]);

                        $details = $cdq->all();
                        //var_dump(microtime(true) - $start."s time taken to run details query clone\n");

                        if ($details) {
                            $this->liquidateVendorItems($liq, $details);
                            //var_dump(microtime(true) - $start."s time taken to liquidate vendor items\n");
                        }

                        //Si el total es 0, lo borramos
                        if (!($liq->total > 0)) {
                            $liq->delete();
                        }
                        //var_dump(microtime(true) - $start."s time taken to run $vendor->name\n");
                        //die();
                    }
                    //break;
                }
                $transaction->commit();
                
            } catch (\Exception $e){
                $transaction->rollback();
                throw $e;
            }
            
            $this->redirect(['index', 'VendorLiquidationSearch[period]' => $model->period]);
            
        }
        
        return $this->render('batch', ['model' => $model]);
    }
    
    /**
     * Genera un item de liquidacion
     * @param type $liq
     * @param type $detail
     */
    private function liquidateVendorItems($liq, $details)
    {
        //$start = microtime(true);
        $vendor_liquidation_items = [];

        $currentCompanyOwner = isset(Yii::$app->params['gestion_owner_company']) ? Yii::$app->params['gestion_owner_company'] : null;
        if(is_null($currentCompanyOwner)){
            Yii::$app->session->addFlash('error', 'Company owner is not setted in the Params file.');
        }

        foreach($details as $detail){
            
            $price = $detail->product->getPriceFromDate($detail->date)->one();
            //var_dump(microtime(true) - $start."s time taken to run getpricefromdate\n");
            //Si el precio del producto es mayor a 0
            //var_dump($price->finalPrice);
            if($price->finalPrice > 0){
                $contract = $detail->contract;
                $customer = $contract->customer;
                
                //Por problemas con datos migrados, agregamos esta cond de customer_id > 22200
                if($contract->status == 'active' && $this->hasPayedFirstBill($customer, $detail, $liq)){

                        /**
                         * context: this was the previous condition. 
                         * i didnt want to remove it so i negated it and made the configuration item for current company name,
                         * so as not to conflict with other companies that werent Westnet.
                         * 
                         * "Por problemas con datos migrados, agregamos esta cond de customer_id > 22200"
                         * "if($customer->customer_id > 22200 && $this->hasPayedFirstBill($customer, $detail, $model)){"
                         */

                    // jumps all customers below ID:22200 when the company is westnet.
                    if($currentCompanyOwner == 'westnet'){
                        if(!($customer->customer_id > 22200)) continue;
                    }

                    $product = $detail->product;
                    $amount = 0.0;

                    //Si es un plan, la comision se calcula por vendedor (VendorCommission
                    if($product->type == 'plan'){
                        $amount = $liq->vendor->commission->calculateCommission($price->finalPrice);
                    //Si es un producto, la comision se calcula por producto (solo si el producto tiene asociada una comision)
                    }else{
                        if($product->commission){
                            $amount = $product->commission->calculateCommission($price->finalPrice);
                        }
                    }
                    //var_dump(microtime(true) - $start."s time taken to run calculate commission\n");

                    $liqItem = [
                        'contract_detail_id' => $detail->contract_detail_id,
                        'description' => $product->name,
                        'vendor_liquidation_id' => $liq->vendor_liquidation_id,
                        'amount' => $amount
                    ];

                    array_push($vendor_liquidation_items, $liqItem);
                }
            }
        }

        VendorLiquidation::batchInsertLiquidationItems($vendor_liquidation_items);
        //var_dump(microtime(true) - $start."s time taken to run batchinsertliquidationitems\n");

    }
    
    
    /**
     * Liquidacion por lotes
     * @return mixed
     */
    public function actionPreview($vendor_id)
    {
        Yii::setLogger(new EmptyLogger());
        
        $model = new VendorLiquidation();
        $model->vendor_id = $vendor_id;
        
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            
            //Query para buscar vendedores
            $vendor = $model->vendor;
            
            $year = Yii::$app->formatter->asDate($model->period, 'yyyy');
            $month = Yii::$app->formatter->asDate($model->period, 'MM');

            //Detalles del mes correspondiente al periodo
            $query = ContractDetail::find();
            $query
                ->leftJoin('vendor_liquidation_item', 'contract_detail.contract_detail_id = vendor_liquidation_item.contract_detail_id')
                ->leftJoin('contract c', 'c.contract_id = contract_detail.contract_id')
                ->where('vendor_liquidation_item.vendor_liquidation_id IS NULL')
                ->andWhere("date_format(contract_detail.date, '%Y%m') <= ".$year.$month)
                ->andWhere(['contract_detail.vendor_id' => $vendor->vendor_id])
                ->andWhere(['c.status' => 'active'])
            ;
            
            $details = $query->all();
            $total = 0.0;
            
            $customers = [];
                        
            $items = [];

            $currentCompanyOwner = isset(Yii::$app->params['gestion_owner_company']) ? Yii::$app->params['gestion_owner_company'] : null;
            if(is_null($currentCompanyOwner)){
                Yii::$app->session->addFlash('error', 'Company owner is not setted in the Params file.');
            }

            //Por cada vendedor
            foreach($details as $detail){

                $price = $detail->product->getPriceFromDate($detail->date)->one();

                //Si el precio del producto es mayor a 0
                if($price->finalPrice > 0){

                    $contract = $detail->contract;
                    $customer = $contract->customer;
                    
                        /**
                         * context: this was the previous condition. 
                         * i didnt want to remove it so i negated it and made the configuration item for current company name,
                         * so as not to conflict with other companies that werent Westnet.
                         * 
                         * "Por problemas con datos migrados, agregamos esta cond de customer_id > 22200"
                         * "if($customer->customer_id > 22200 && $this->hasPayedFirstBill($customer, $detail, $model)){"
                         */

                    // jumps all customers below ID:22200 when the company is westnet.
                    if($currentCompanyOwner == 'westnet'){
                        if(!($customer->customer_id > 22200)) continue;
                    }

                    if($this->hasPayedFirstBill($customer, $detail, $model)){

                        $liqItem = new \app\modules\westnet\models\VendorLiquidationItem();
                        $liqItem->contract_detail_id = $detail->contract_detail_id;
                        $liqItem->description = $detail->product->name;

                        //Si es un plan, la comision se calcula por vendedor (VendorCommission)
                        if($detail->product->type == 'plan'){
                            $liqItem->amount = $vendor->commission->calculateCommission($price->finalPrice);

                        //Si es un producto, la comision se calcula por producto (solo si el producto tiene asociada una comision)
                        }else{
                            if($detail->product->commission){
                                $liqItem->amount = $detail->product->commission->calculateCommission($price->finalPrice);
                            }
                        }

                        //Las comisiones para productos con precio, pero con valor 0 (por config de comision), se deben registrar
                        if(!empty($liqItem->amount)){

                            $total += $liqItem->amount;
                            $items[] = $liqItem;
                            
                            $customers[$customer->customer_id] = $customer->fullName;
                            
                        }

                    }
                }
            }
            
            $dataProvider = new ArrayDataProvider([
                'allModels' => $items,
                'pagination' => false,
            ]);
            
            return $this->render('preview', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'total' => $total,
                'customers' => $customers
            ]);

                
        }
        
        return $this->render('preview-form', ['model' => $model]);
    }
    
    /**
     * Buscamos la primer factura luego de que fue generado el detalle de
     * contrato. Si los pagos posteriores superan el valor de la primer factura,
     * significa que se encuentra pagada.
     * @param type $customer
     * @param type $contractDetail
     * @return boolean
     */
    private function hasPayedFirstBill($customer, $contractDetail, $liquidation)
    {
        
        $paymentModel = new PaymentSearch();
        $paymentModel->customer_id = $customer->customer_id;
        
        //Consideramos el saldo hasta el ultimo dia habil del mes que se esta liquidando
        $toDate = (new \DateTime( $liquidation->period ))->format('Y-m-t');
        $fromDate = (new \DateTime( $contractDetail->date ))->format('Y-m-d');

        //Facturas desde la fecha de contrato; una factura anterior no incluiria este contrato
        $billed = $paymentModel->accountTotalCredit($fromDate, $toDate);
        
        //Si no se ha facturado nada, devolvemos false
        if($billed == 0){
            return false;
        }
        
        //Pagos desde la fecha de contrato, porque el pago puede existir antes de la factura
        $payed = $paymentModel->accountPayed($fromDate, $toDate);
        
        if(($billed - $payed) < Yii::$app->params['account_tolerance']){
            return true;
        }else{
            return false;
        }
        
    }

    /**
     * Finds the VendorLiquidation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VendorLiquidation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VendorLiquidation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Anula un item de liquidacion, poniendo en 0 el importe.
     * @param integer $id
     * @return mixed
     */
    public function actionCreateBill($id)
    {
        $model = $this->findModel($id);

        if($model){
            if(!$model->vendor->provider_id) {
                Yii::$app->session->addFlash('error', Yii::t('westnet', 'Provider is not set.'));
                return $this->actionView($id);
            }
            if($model->status != VendorLiquidation::VENDOR_LIQUIDATION_DRAFT && $model->status != VendorLiquidation::VENDOR_LIQUIDATION_SUCCESS) {
                Yii::$app->session->addFlash('error', Yii::t('westnet', 'The liquidation status must be draft.'));
                return $this->actionView($id);
            }

            $service = VendorLiquidationService::getInstance();
            if($service->registerBill($model) ) {
                return $this->redirect(['view', 'id' => $model->vendor_liquidation_id]);
            } else {
                foreach ( $service->messages as $key=>$message ) {
                    Yii::$app->session->addFlash('error', Yii::t('westnet', $message));
                }

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

        }
    }

    /**
    * Liquidacion por lotes
    * @return mixed
    **/
    public function actionBatchVendorLiquidationProcess()
    {
        Yii::setLogger(new EmptyLogger());
        $model = new VendorLiquidationProcess();


        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();

            $model->period = (new \DateTime($model->period))->format('Y-m-d H:i:s');
            $model->status = 'draft';
            try{
                if ($model->save()) {
                    \Yii::$app->session->setFlash(
                        'success',
                        "Process created successfully\n"
                    );
    
                    $vendors = $model->findVendorsSQL();
                    foreach ($vendors as $vendor) {
                        VendorLiquidation::createVendorLiquidationSQL($vendor['vendor_id'], $model->period, $model->vendor_liquidation_process_id);
                    }
                    
                } else {
                    /* var_dump($model->getErrorSummary(true));
                    die(); */
                    \Yii::$app->session->setFlash(
                        'error',
                        "Process failed to save"
                    );
                }
                $transaction->commit();
            }catch ( \Exception $e){
                $transaction->rollBack();
            }

            return $this->redirect(Url::to(['vendor-liquidation-process'])); // relative redirect to view (works regardless of pretty url)
        }
        return $this->render('batch', ['model' => $model]);
    }


    /**
     * Liquidacion por lotes
     * @return mixed
     */
    public function actionVendorLiquidationProcess()
    {
        Yii::setLogger(new EmptyLogger());

        $dataProvider = new ArrayDataProvider([
            'allModels' => VendorLiquidationProcess::find()
                ->orderBy([
                    'vendor_liquidation_process_id' => SORT_DESC // added sort to give first the newer processes
                ])
                ->all(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $this->render('vendor-liquidation-process', [
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionChangeStatusLiquidation($id){
        $model = VendorLiquidationProcess::findOne(['vendor_liquidation_process_id' => $id]);
        
        $model->status = 'pending';
        $model->save(false);

        $vendor_liquidations = VendorLiquidation::find()->where(['period' => $model->period, 'vendor_liquidation_process_id' => $model->vendor_liquidation_process_id])->all();
        
        foreach ($vendor_liquidations as $value) {
            VendorLiquidation::UpdateStatusVendorLiquidation('pending',$value->vendor_liquidation_id);
        }
        $this->redirect(Url::to(['vendor-liquidation-process'])); 
    }

    public function actionRemoveVendorLiquidationProcess($id){
        $model = VendorLiquidationProcess::findOne(['vendor_liquidation_process_id' => $id]);

        $vendor_liquidations = VendorLiquidation::find()->where(['period' => $model->period, 'vendor_liquidation_process_id' => $model->vendor_liquidation_process_id])->all();
        
        foreach ($vendor_liquidations as $value) {
            $value->delete();
        }

        $model->delete();

        $this->redirect(Url::to(['vendor-liquidation-process'])); 
    }

    public function actionViewVendorLiquidationProcess($id){
        $model = VendorLiquidationProcess::findOne(['vendor_liquidation_process_id' => $id]);
    
        return $this->render('view-vendor-liquidation-process',['model' => $model]);
    }

    public function actionStatusVendorLiquidationProcess(){
        $data = ArrayHelper::map(VendorLiquidation::ProgressStatusVendorLiquidation(Yii::$app->request->post()['id']),'status', 'cantidad');
        $cant_status = [
            'pending' => isset($data['pending']) ? $data['pending'] : 0, 
            'success' => isset($data['success']) ? $data['success'] : 0, 
            'cancelled' => isset($data['cancelled']) ? $data['cancelled'] : 0
        ];

        return json_encode($data);
    }
}
