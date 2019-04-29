<?php

namespace app\modules\pagomiscuentas\controllers;

use app\components\web\Controller;
use Yii;
use app\modules\pagomiscuentas\models\PagomiscuentasLiquidation;
use app\modules\pagomiscuentas\models\search\PagomiscuentasLiquidationSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PagomiscuentasLiquidationController implements the CRUD actions for PagomiscuentasLiquidation model.
 */
class PagomiscuentasLiquidationController extends Controller
{

    /**
     * Lists all PagomiscuentasLiquidation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PagomiscuentasLiquidationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PagomiscuentasLiquidation model.
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
     * Creates a new PagomiscuentasLiquidation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PagomiscuentasLiquidation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pagomiscuentas_liquidation_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PagomiscuentasLiquidation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pagomiscuentas_liquidation_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PagomiscuentasLiquidation model.
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
     * Finds the PagomiscuentasLiquidation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PagomiscuentasLiquidation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PagomiscuentasLiquidation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
