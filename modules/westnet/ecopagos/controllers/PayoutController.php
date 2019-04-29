<?php

namespace app\modules\westnet\ecopagos\controllers;

use Yii;
use app\modules\westnet\ecopagos\models\Payout;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\ecopagos\models\search\PayoutSearch;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Justification;

/**
 * PayoutController implements the CRUD actions for Payout model.
 */
class PayoutController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Payout models.
     * @return mixed
     */
    public function actionIndex() {
        $this->layout = '/fluid';

        $searchModel = new PayoutSearch();
        $searchModel->scenario = PayoutSearch::SCENARIO_ADMIN;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $total = (new \yii\db\Query())->select([new \yii\db\Expression('sum(a.amount) as total')])
                ->from(['a' => $dataProvider->query])->scalar($searchModel->getDb());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'total' => $total,
        ]);
    }

    /**
     * Lists all Payout models.
     * @return mixed
     */
    public function actionPayoutList() {
        $this->layout = '/fluid';

        $searchModel = new \app\modules\westnet\ecopagos\models\search\PayoutSearch();
        //$searchModel->scenario = Payout::SCENARIO_SEARCH;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('payout_list', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Lists all Payout models.
     * @return mixed
     */
    public function actionDailyPayoutList() {

        $searchModel = new \app\modules\westnet\ecopagos\models\search\PayoutSearch();
        //$searchModel->scenario = Payout::SCENARIO_SEARCH;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('daily_payout_list', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single Payout model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $allowed = true;
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => Justification::find()->where(['payout_id' => $id])->all(),
        ]);

        $current_cashier = \app\modules\westnet\ecopagos\models\Cashier::findOne(['user_id' => Yii::$app->user->id]);
        if ($current_cashier) {
            $allowed = $model->canView($current_cashier->cashier_id);
        }

        if ($allowed) {
            return $this->render('view', [
                        'model' => $model,
                        'dataProvider' => $dataProvider
            ]);
        } else {
            throw new \yii\web\ForbiddenHttpException('No tiene permisos para ver esta información.');
        }
    }

    /**
     * Creates a new Payout model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        $ecopago = \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->ecopago;

        //Checks if this ecopago can recieve more payouts
        if (!$ecopago->isOnLimit()) {
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Payout cannot be completed. Ecopago payout limit reached.'));
            $this->redirect(['index']);
        }

        $model = new Payout();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->session->setFlash("success", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Payout successfully created!'));
            return $this->redirect(['view', 'id' => $model->payout_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Payout model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->payout_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Reverses a payout, setting its state to reversed
     * @return mixed
     */
    public function actionReverse($id) {

        $model = $this->findModel($id);

        if ($model->reverse()) {
            Yii::$app->session->setFlash("success", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Payout successfully reversed!'));
            return $this->redirect(['view', 'id' => $model->payout_id]);
        } else {
            Yii::$app->session->setFlash("warning", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Reverse payout operation could not be completed'));
            return $this->redirect(['view', 'id' => $model->payout_id]);
        }
    }

    /**
     * Deletes an existing Payout model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionAjaxInfo($number) {

        if (\Yii::$app->request->isAjax) {

            $model = $this->findModel($number);

            $json = [];
            $json['status'] = 'success';
            $json['html'] = $this->renderAjax('ajax_view', [
                'model' => $model,
            ]);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $json;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Payout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Payout::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
