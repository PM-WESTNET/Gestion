<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\ProductPrice;
use app\modules\sale\models\search\ProductPriceSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductPriceController implements the CRUD actions for ProductPrice model.
 */
class ProductPriceController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all ProductPrice models.
     * @return mixed
     */
    public function actionIndex($product_id)
    {
        return $this->redirect(['product/price-history','id'=>$product_id]);
    }
    
    
    /**
     * Lists all StockMovement models.
     * @return mixed
     */
    public function actionGraph($product_id = null)
    {
        $searchModel = new ProductPriceSearch;
        
        if($product_id !== null){
            $searchModel->product_id = $product_id;
        }
        
        $dataProvider = $searchModel->graphSearch(Yii::$app->request->getQueryParams());
        
        $first = array_shift($dataProvider->getModels());
        $last = array_pop($dataProvider->getModels());
        
        //Periodos
        $graphData = new \app\components\helpers\GraphData();
        $graphData->fromdate = $first->date;
        $graphData->todate = $last->date;
        
        //Datos
        $graphData->dataProvider = $dataProvider;
        $graphData->yAttribute = 'net_price';
        $graphData->xAttribute = 'date';
        $graphData->colorAttribute = 'rgb';
        $graphData->idAttribute = 'product_id';
        
        return $this->render('graph', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'graphData' => $graphData,
        ]);
    }


    /**
     * Displays a single ProductPrice model.
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
     * Creates a new ProductPrice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($product_id)
    {
        $product = \app\modules\sale\models\Product::findOne($product_id);
        $model = new ProductPrice;

        if ($model->load(Yii::$app->request->post()) && $product->link('productPrices',$model)) {
            return $this->redirect(['product/view', 'id' => $product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'product' => $product
            ]);
        }
    }

    /**
     * Updates an existing ProductPrice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $new_model = new ProductPrice;

        if ($new_model->load(Yii::$app->request->post())) {
            if($new_model->price != $model->price)
                if($model->product->link('productPrices',$new_model))
                    return $this->redirect(['product/view', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProductPrice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        //Necesitamos el product_id para redireccionar
        $product_id = $model->product_id;
        $model->delete();

        return $this->redirect(['product/price-history','id'=>$product_id]);
    }

    /**
     * Finds the ProductPrice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductPrice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductPrice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
