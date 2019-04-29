<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\PointOfSale;
use app\modules\sale\models\search\PointOfSaleSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PointOfSaleController implements the CRUD actions for PointOfSale model.
 */
class PointOfSaleController extends Controller
{

    /**
     * Lists all PointOfSale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PointOfSaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PointOfSale model.
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
     * Creates a new PointOfSale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($company = null)
    {
        $model = new PointOfSale();
        
        if($company){
            $model->company_id = (int)$company;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_of_sale_id]);
        } else {
            
            //Si la empresa aun no tiene pto de venta, tildamos "por defecto"
            $companyModel = \app\modules\sale\models\Company::findOne($company);
            if($companyModel && !$companyModel->defaultPointOfSale){
                $model->default = true;
            }
            
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PointOfSale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_of_sale_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PointOfSale model.
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
     * Finds the PointOfSale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PointOfSale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PointOfSale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
