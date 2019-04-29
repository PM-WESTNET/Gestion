<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use app\modules\westnet\ecopagos\models\PeriodClosure;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PeriodClosureController implements the CRUD actions for PeriodClosure model.
 */
class PeriodClosureController extends Controller
{

    /**
     * Lists all PeriodClosure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PeriodClosure::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PeriodClosure model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $payoutSearch = new \app\modules\westnet\ecopagos\models\search\PayoutSearch();
        $currentCashier = $payoutSearch->getCurrentCashier();
        if ($model->canView($currentCashier->cashier_id)) {
            return $this->render('view', [
                        'model' => $model,
            ]);
        }else{
            throw  new \yii\web\ForbiddenHttpException('No tiene permisos para ver esta informacion');
        }
    }

    /**
     * Creates a new PeriodClosure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PeriodClosure();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->period_closure_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PeriodClosure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->period_closure_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PeriodClosure model.
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
     * Finds the PeriodClosure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PeriodClosure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PeriodClosure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
