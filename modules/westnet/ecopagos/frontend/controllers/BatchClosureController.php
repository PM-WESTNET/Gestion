<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\BatchClosure;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use \app\modules\westnet\ecopagos\models\Collector;

/**
 * BatchClosureController implements the CRUD actions for BatchClosure model.
 */
class BatchClosureController extends BaseController {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action))
            return true;
        else
            return false;
    }

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all BatchClosure models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\modules\westnet\ecopagos\models\search\BatchClosureSearch();
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
    public function actionView($id, $from = "*")
    {
        $model = $this->findModel($id);
        if ($model->ecopago_id != UserHelper::getCashier()->ecopago_id) {
            Yii::$app->session->setFlash('error', 'El cierre de lote no corresponte al ecopago actual');
            return $this->redirect(['index']);
        }
        return $this->render('view', [
            'model' => $model,
            'from' => $from
        ]);
    }

    /**
     * Displays a list of payouts for a specific BatchClosure
     * @param integer $id
     * @return mixed
     */
    public function actionViewPayouts($id) {
        return $this->render('view_payouts', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BatchClosure model.
     * If execution is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        $model = new BatchClosure();
        $model->scenario = BatchClosure::SCENARIO_FRONTEND;
        $model->ecopago_id = UserHelper::getCashier()->ecopago_id;

        $wasExecuted = false;

        if (Yii::$app->request->post()) {

            //Find real collector
            $collector = new Collector();
            $collector->load(Yii::$app->request->post());
            $realCollector = $collector->find()->where([
                        'number' => $collector->number,
                    ])->one();

            $model->collector_id = $realCollector->collector_id;

            //Find preview
            $model->preview();

            //Try an execution
            $wasExecuted = $model->execute();
        }

        if ($wasExecuted) {

            Yii::$app->session->setFlash("success", EcopagosModule::t('app', 'Batch closure successfully executed!'));
            return $this->redirect(['view', 'id' => $model->batch_closure_id, 'from' => 'create']);
        } else {

            if ($model->hasErrors())
                Yii::$app->session->setFlash("error", EcopagosModule::t('app', 'Batch closure could not be executed'));

            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BatchClosure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->batch_closure_id]);
        } else {
            return $this->render('update', [
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
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Batch closure successfully canceled!'));
        } else {
            Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'Batch closure could not be canceled'));
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
        Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Batch closure successfully deleted!'));
        return $this->redirect(['index']);
    }

    /**
     * Returns something on ajax calls
     * @param type $id
     */
    public function actionGetPreview() {

        if (\Yii::$app->request->isAjax) {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            //Find real collector
            $collector = new \app\modules\westnet\ecopagos\models\Collector();
            $collector->load(Yii::$app->request->post());
            $realCollector = $collector->find()->where([
                        'number' => $collector->number,
                    ])->one();

            //Builds a batch closure preview
            $batchPreview = new BatchClosure();
            $batchPreview->ecopago_id = \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->ecopago_id;
            $batchPreview->preview();

            $json = [];
            if ($batchPreview->hasPayments && !empty($realCollector)) {

                $batchPreview->collector_id = $realCollector->collector_id;

                $json['status'] = 'success';
                $json['html'] = $this->renderAjax('details', [
                    'model' => $batchPreview,
                    'collector' => $realCollector
                ]);
            } else {
                $json['status'] = 'warning';
                $json['message'] = \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'No payouts found');
            }

            return $json;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
