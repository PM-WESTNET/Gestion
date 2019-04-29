<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductPrice;
use app\modules\sale\models\search\ProductSearch;
use app\modules\sale\models\search\PlanSearch;
use app\modules\sale\models\search\ProductPriceSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';
        
        $searchModel = new ProductSearch();
        $searchModel->stock_company_id = \app\modules\sale\models\Company::findDefault()->company_id;
        
        if(empty($_GET['ProductSearch']['search_text'])){
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        }else{
            $dataProvider = $searchModel->searchText(Yii::$app->request->getQueryParams());
        }
        
//        Yii::$app->set('company', \app\modules\sale\models\Company::findOne($searchModel->stock_company_id));
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product;
        $price = new ProductPrice;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            $price->load(Yii::$app->request->post());
            $model->setPrice($price->net_price, $price->exp_date);
            
            return $this->redirect(['view', 'id' => $model->product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'price' =>$price
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $price = $model->activePrice;
        
        if(empty($price)) $price = new ProductPrice;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            //Nuevo precio
            $newPrice = new ProductPrice;
            $newPrice->load(Yii::$app->request->post());
            
            //Si el nuevo precio difiere del anterior, actualizamos. TODO: funcion
            if(($newPrice->net_price + $model->calculateTaxes($newPrice->net_price)) != ($price->net_price + $price->taxes) || $newPrice->exp_date != $price->exp_date){
                $model->setPrice($newPrice->net_price, $newPrice->exp_date);
            }
            
            return $this->redirect(['view', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'price' => $price
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionBarcode($id){
        
        $model = $this->findModel($id);
        
        header('Content-Type: image/png');
        $model->barcode;
        
    }
    
    public function actionPrintBarcodes($id){
        
        $this->layout = '/print';
        
        $model = $this->findModel($id);
        
        return $this->render('print-barcodes', [
            'model' => $model,
        ]);
    }
    
    public function actionPriceHistory($id){
        
        $model = $this->findModel($id);
        $searchModel = new ProductPriceSearch;
        
        $dataProvider = new \yii\data\ActiveDataProvider(
            [
                'query'=>$model->getProductPrices(),
                'sort'=>[
                    'defaultOrder'=>['product_price_id'=>SORT_DESC]
                ]
            ]
        );
        
        return $this->render('price-history', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
        
    }

    public function actionUpdatePrices($type='product')
    {
        $this->layout = '//fluid';
        
        if($type == 'plan'){
            $searchModel = new PlanSearch;
        }else{
            $searchModel = new ProductSearch;
        }
        
        $reflex = new \ReflectionClass($searchModel);
        
        if(empty($_GET[$reflex->getShortName()]['search_text'])){
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        }else{
            $dataProvider = $searchModel->searchText(Yii::$app->request->getQueryParams());
        }

        return $this->render('update-prices', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        
    }

    /**
     * Permite actualizar el precio de un producto desde la grilla
     * @param type $product_id
     */
    public function actionUpdatePrice($product_id)
    {
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $product = $this->findModel($product_id);
        
        $model = new \app\modules\sale\models\forms\ProductPriceForm();
        
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            
            if($model->net){
                $product->setPrice($model->net, null);
            }else{
                $product->setFinalPrice($model->final, null);
            }
            
            return [
                'status' => 'success',
                'model_id' => $product_id,
                'model' => $product,
                'message' => Yii::t('app', 'Updated')
            ];
            
        }
        
        return [
            'status' => 'error',
            'errors' => $model->getErrors(),
            'model_id' => $product_id,
            'model' => $product
        ];
        
    }
    
    public function actionStock(){
        
        $searchModel = new ProductSearch;
        $searchModel->stock_company_id = \app\modules\sale\models\Company::findDefault()->company_id;

        if(empty($_GET['ProductSearch']['search_text'])){
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        }else{
            $dataProvider = $searchModel->searchText(Yii::$app->request->getQueryParams());
        }
        
//        Yii::$app->set('company', \app\modules\sale\models\Company::findOne($searchModel->stock_company_id));
        
        return $this->render('update-stock', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        
    }
    
    public function actionUpdateStock($product_id)
    {
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = Yii::$app->getModule('sale')->stock->createMoveIn();
        $product = $this->findModel($product_id);
        
        $model->product_id = $product->product_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'success',
                'model' => $model,
                'message' => Yii::t('app', 'Updated'),
                'model_id' => $product_id
            ];
        } else {
            return [
                'status' => 'error',
                'model' => $model,
                'errors' => $model->getErrors(),
                'model_id' => $product_id
            ];
        }
        
    }

    
    public function actionBatchUpdater(){
        
        $model = new \app\modules\sale\models\UpdatePriceFormModel();
        
        if($model->load(Yii::$app->request->post())){
            
            $count = Product::batchUpdate($model->percentage, 'product', $model->filter, $model->exp_date, $model->items, $model->category);
            
            $json = [
                'status'=>'success',
                'count'=>$count
            ];
            
            //No funciona con session->setFlash porque la pagina luego recarga el contenedor con pjax.
            Yii::$app->session->set('updater-messages',['success'=>Yii::t('app','Product prices has been updated succesfully. {count} products updated.',['count'=>$count])]);
            
            echo \yii\helpers\Json::encode($json);
            
            return;
            
        }
        
        echo $this->renderAjax('_batch-updater',[
            'model'=>$model
        ]);
        
    }


    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
