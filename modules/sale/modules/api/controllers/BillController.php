<?php

namespace app\modules\sale\modules\api\controllers;

use app\components\web\RestController;
use app\modules\sale\models\BillDetail;
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

/**
 * BillController implements the CRUD actions for Bill model.
 */
class BillController extends RestController
{
    
    public $modelClass = 'app\modules\sale\models\Bill';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['index']);
        
        return $actions;
    }
    
    /**
     * Lists all Bill models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new BillSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        return $dataProvider;
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

        return $dataProvider;
    }

    /**
     * Creates a new Bill model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = BillExpert::createBill(Yii::$app->request->post('Bill[bill_type_id]'));
        $model->load(Yii::$app->request->post());
        
        if(!$model->save()){
            throw new \yii\web\HttpException(500, Yii::t('app','Error saving the new bill.'));
        }
        
        $details = Yii::$app->request->post('Bill[billDetails]');
        foreach($details as $detail){
            $this->addProductDetail($model, $detail['product_id'], $detail['qty']);
        }
        
        return $model;
        
    }
    
    /**
     * Imports Bills model.
     * @return mixed
     */
    public function actionImport()
    {
        
        $bills = Yii::$app->request->post('Bills');
        $count = 0;
        $errors = 0;
        $count_array = array();
        $errors_array = array();

        foreach($bills as $bill){
            $model = BillExpert::createBill($bill['bill_type_id']);
            $model->attributes = $bill;
            $model->status = 'draft';

            $model->customer_id = isset($bill['customer_id']) ? $bill['customer_id'] : null;
            
            if($model->customer && $model->customer->checkBillType($model->billType) && $model->save()){
                $count++;                
                $bill['bill_id'] = $model->bill_id;                 
                array_push($count_array, $bill);
            }else{
                $errors++;
                array_push($errors_array, $bill);
            }
            
            $details = $bill['billDetails'];
            foreach($details as $detail){
                    $product = \app\modules\sale\models\Product::findOne($detail['product_id']);
                
                //Product detail
                if($product){
                    $this->addProductDetail($model, $product, $detail['qty']);
                
                //Manual detail
                }else{
                    $model->addDetail([
                        'concept' => $detail['concept'],
                        'unit_net_price' => $detail['unit_net_price'],
                        'unit_final_price' => $detail['unit_final_price'],
                        'qty' => $detail['qty']
                    ]);
                }
            }
            $model->pending();
        }
        
        return [
            'status' => 'success',
            'imported' => $count,
            'errors' => $errors,
            'count_array' => $count_array,
            'errors_array' => $errors_array,
        ];
        
    }
        
    
    public function actionClose($id){
        
        $model = $this->findModel($id);

        if(!empty($model->billDetails) && $model->status != 'closed'){
            $model->close();
        }
        
        return $model;
            
    }
    
    /**
     * Permite agregar un producto a la factura
     * @param Bill $model
     * @param Product $product
     * @param float $qty
     * @return type
     */
    private function addProductDetail($model, $product, $qty = 1)
    {
        if($product->secondary_unit_id){
            $detail = $model->addDetail([
                'product_id'=>$product->product_id,
                'concept'=>$product->name,
                'unit_net_price'=>$product->netPrice,
                'unit_final_price'=>$product->finalPrice,
                'secondary_qty'=>$qty
            ]);
        }else{
            $detail = $model->addDetail([
                'product_id'=>$product->product_id,
                'concept'=>$product->name,
                'unit_net_price'=>$product->netPrice,
                'unit_final_price'=>$product->finalPrice,
                'qty'=>$qty
            ]);
        }

        return $detail;
        
    }
    
    /**
     * 
     * @return \yii\data\ActiveDataProvider
     */
    public function actionTypes()
    {
        
        $query = \app\modules\sale\models\BillType::find();
        $query->where(['startable' => 1]);
        
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
        
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

}
