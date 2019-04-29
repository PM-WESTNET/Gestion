<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use app\modules\westnet\ecopagos\models\DailyClosure;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * DailyClosureController implements the CRUD actions for DailyClosure model.
 */
class DailyClosureController extends BaseController {

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
     * Tries to open a cash register for this cashier on an specific ecopago branch
     */
    public function actionOpenCashRegister() {

        $dailyClosure = new DailyClosure();

        if ($dailyClosure->openCashRegister()) {
            Yii::$app->session->setFlash("success", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Cash registered successfully open!'));
            return $this->redirect(\yii\helpers\Url::to(['/westnet/ecopagos/frontend/payout/create']));
        } else {
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Cash registered could not be open'));
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Gets the preview for closing the current cash register on a specific ecopago branch
     */
    public function actionPreview() {

        if (UserHelper::hasOpenCashRegister()) {
            $dailyClosure = UserHelper::getOpenCashRegister();
            $dailyClosure->preview();
            return $this->render('preview', [
                        'model' => $dailyClosure,
            ]);
        } else {
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Open cash register could not be found'));
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Tries to close the current cash register on a specific ecopago branch
     */
    public function actionClose() {

        if (UserHelper::hasOpenCashRegister()) {

            $dailyClosure = UserHelper::getOpenCashRegister();

            if ($dailyClosure->close()) {
                Yii::$app->session->setFlash("success", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Daily closure successfully executed!'));
                return $this->render('view', ['model'=> $dailyClosure, 'from'=> 'close']);
            } else {
                Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Could not execute daily closure'));
                return $this->redirect(['index']);
            }
        } else {
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Open cash register could not be found'));
            return $this->redirect(['site/index']);
        }
    }

    /**
     * Lists all DailyClosure models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new \app\modules\westnet\ecopagos\models\search\DailyClosureSearch();
        $searchModel->scenario = DailyClosure::SCENARIO_FRONTEND;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single DailyClosure model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        if ($model->ecopago_id != \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->ecopago_id) {
            Yii::$app->session->setFlash('error', 'El cierre diario no corresponte al ecopago actual');
            return $this->redirect(['index']);
        }
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new DailyClosure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new DailyClosure();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->daily_closure_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Cancels a daily closure (if possible)
     * @param type $id
     * @return mixed
     */
    public function actionCancel($id) {

        $dailyClosure = $this->findModel($id);

        //We need to know if this batch closure is cancelable and whether can be canceled or not
        if ($dailyClosure->isCancelable() && $dailyClosure->cancel()) {
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Daily closure successfully canceled!'));
        } else {
            Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'Daily closure could not be canceled'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing DailyClosure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DailyClosure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DailyClosure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = DailyClosure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
