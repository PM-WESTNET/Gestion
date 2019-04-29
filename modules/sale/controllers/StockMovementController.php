<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\StockMovement;
use app\modules\sale\models\search\StockMovementSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockMovementController implements the CRUD actions for StockMovement model.
 */
class StockMovementController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all StockMovement models.
     * @return mixed
     */
    public function actionIndex($product_id = null)
    {
        $this->layout = '//fluid';
        
        $searchModel = new StockMovementSearch;
        
        if($product_id !== null){
            $searchModel->product_id = $product_id;
        }
        
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all StockMovement models.
     * @return mixed
     */
    public function actionGraph($product_id = null)
    {
        $searchModel = new StockMovementSearch;
        
        if($product_id !== null){
            $searchModel->product_id = $product_id;
        }
        
        $dataProvider = $searchModel->graphSearch(Yii::$app->request->getQueryParams());
        
        $first = array_shift($dataProvider->getModels());
        $last = array_pop($dataProvider->getModels());
        
        //Periodos
        $graphData = new \app\components\helpers\GraphData();
        
        if($first != null and $last != null){
            $graphData->fromdate = $first->date;
            $graphData->todate = $last->date;
        }
        
        //Datos
        $graphData->dataProvider = $dataProvider;
        $graphData->yAttribute = 'balance';
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
     * Displays a single StockMovement model.
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
     * Creates a new StockMovement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($product_id, $type = null)
    {
        $model = Yii::$app->getModule('sale')->stock->createMove();

        $product = \app\modules\sale\models\Product::findOne($product_id);
        
        if($product === null)
            throw new NotFoundHttpException('The requested page does not exist.');

        if($type !== null)
            $model->type = $type;
        
        $model->product_id = $product->product_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->stock_movement_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Deletes an existing StockMovement model.
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
     * Finds the StockMovement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockMovement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockMovement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
