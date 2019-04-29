<?php

namespace app\modules\westnet\ecopagos\controllers;

use Yii;
use app\modules\westnet\ecopagos\models\BatchClosure;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\ecopagos\models\search\BatchClosureSearch;

/**
 * BatchClosureController implements the CRUD actions for BatchClosure model.
 */
class BatchClosureController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all BatchClosure models.
     * @return mixed
     */
    public function actionIndex() {
        $this->layout = '/fluid';

        $searchModel = new BatchClosureSearch();
        $searchModel->scenario = BatchClosureSearch::SCENARIO_ADMIN;

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single BatchClosure model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Renders a batch closure, after a manual validation
     * @param integer $id
     * @return mixed
     */
    public function actionRender($id) {

        $model = $this->findModel($id);
        $model->scenario = BatchClosure::SCENARIO_RENDER;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->render()) {
                Yii::$app->session->addFlash("success", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure successfully rendered!'));
            }

            return $this->redirect(['view', 'id' => $model->batch_closure_id]);
        } else {

            if ($model->hasErrors())
                Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Could not render batch closure, please complete all fields'));

            return $this->render('render', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Cancels a batch closure (if possible)
     * @param type $id
     * @return type
     */
    public function actionCancel($id) {

        $batchClosure = $this->findModel($id);

        //We need to know if this batch closure is cancelable and whether can be canceled or not
        if ($batchClosure->isCancelable() && $batchClosure->cancel()) {
            Yii::$app->getSession()->setFlash('success', \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure successfully canceled!'));
        } else {
            Yii::$app->getSession()->setFlash('error', \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure could not be canceled'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing BatchClosure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BatchClosure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BatchClosure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = BatchClosure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
