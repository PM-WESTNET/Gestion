<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Payout;
use app\modules\westnet\ecopagos\models\search\PayoutSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\westnet\ecopagos\models\Justification;
use app\modules\config\models\Config;

/**
 * PayoutController implements the CRUD actions for Payout model.
 */
class PayoutController extends BaseController {

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
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {

            return true;
        } else
            return false;
    }

    /**
     * Lists all Payout models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new PayoutSearch();

        $searchModel->cashier_id = $searchModel->getCurrentCashier()->cashier_id;
        $searchModel->ecopago_id = $searchModel->getCurrentCashier()->ecopago_id;

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dailyClosure = $searchModel->getCurrentCashier()->currentDailyClosure();

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'dailyClosure' => $dailyClosure,
                    'reversed' => Payout::countReversed()
        ]);
    }

    /**
     * Displays a single Payout model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $from = 'view') {
        $payoutSearch= new PayoutSearch();
        $current_cashier = $payoutSearch->getCurrentCashier();
        $model = $this->findModel($id);
        $min_justification_length = Config::getValue('justification_length');
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => Justification::find()->where(['payout_id' => $model->payout_id])->all(),
        ]);

        if ($model->canView($current_cashier->cashier_id)) {
            return $this->render('view', [
                        'model' => $model,
                        'from' => $from,
                        'dataProvider' => $dataProvider,
                        'min_justification_length' => $min_justification_length
            ]);
        } else {
            throw new ForbiddenHttpException('No tiene permisos para ver esta información.');
        }
    }

    /**
     * Prints a ticket
     * @param type $id
     * @return type
     */
    public function actionPrint($id) {

        $model = $this->findModel($id);

        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payout model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($last_payout=null) {

        //Check the cash register
        $this->checkCashRegister();

        $ecopago = UserHelper::getCashier()->ecopago;

        //Checks if this ecopago can recieve more payouts
        if (!$ecopago->isOnLimit()) {
            Yii::$app->session->setFlash("error", EcopagosModule::t('app', 'Payout cannot be completed. Ecopago payout limit reached.'));
            $this->redirect(['index']);
        }

        $model = new Payout();  

        $min_justification_length = Config::getValue('justification_length');
        $cashier_id = $model->getCurrentCashier()->cashier_id;
        $ecopago_id = $model->getCurrentCashier()->ecopago_id;
        $model->cashier_id = $cashier_id;
        $model->ecopago_id = $ecopago_id;
        $dailyClosure = $model->getCurrentCashier()->currentDailyClosure();
        Yii::$app->session->set('saveTimes', 0);
        if ($model->load(Yii::$app->request->post())) {
            $transaction= Yii::$app->getDb()->beginTransaction();
            if($model->save()){
                $transaction->commit();
                $model->refresh();
                $dailyClosure->refresh();
                $oldModel = $model;
                $model = new Payout();
                $model->cashier_id = $model->getCurrentCashier()->cashier_id;
                $model->ecopago_id = $model->getCurrentCashier()->ecopago_id;

                return $this->redirect(['create', 'last_payout' => $oldModel->payout_id]);
            }else{
                $transaction->rollBack();
                \Yii::$app->session->setFlash('error', EcopagosModule::t('app', 'Two payment for same customer are not allowed in same box'));
                return $this->redirect(['create']);
            
            }
        } else {
            $params = [
                'model' => $model,
                'dailyClosure' => $dailyClosure,
                'reversed' => Payout::countReversed(),
                'min_justification_length' => $min_justification_length
            ];

            if($last_payout) {
                $params['oldModel'] = $this->findModel($last_payout);
                $params['from'] = 'create';
            }

            return $this->render('create', $params);
        }
    }

    /**
     * Reverses a payout, setting its state to reversed
     * @return mixed
     */
    public function actionReverse($id, $cause, $reprint) {

        //Check the cash register
        $this->checkCashRegister();

        $model = $this->findModel($id);
        Yii::$app->session->set('saveTimes', 0);
        if ($model->reverse()) {
            $justification = Justification::newJustification($id, $cause, $reprint);
            Yii::$app->session->setFlash("success", EcopagosModule::t('app', 'Payout successfully reversed!'));
            return $this->redirect(['view', 'id' => $model->payout_id]);
        } else {
            Yii::$app->session->setFlash("warning", EcopagosModule::t('app', 'Reverse payout operation could not be completed'));
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

            \Yii::$app->response->format = Response::FORMAT_JSON;
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
    
    public function actionGetPrintLayout($payout_id)
    {
        Yii::$app->response->format = 'json';

        $payout = Payout::findOne($payout_id);
        $result = $payout->getPrintLayout();

        return $result;
    }

    public function actionSaveJustification()
    {
        Yii::$app->response->format = 'json';

        $payout_id = Yii::$app->request->post('payout_id');
        $cause = Yii::$app->request->post('cause');
        $re_print = Yii::$app->request->post('reprint');
        $model = Justification::newJustification($payout_id, $cause, $re_print);

        return [
            'justification_id' => $model->justification_id
        ];
    }

    public function actionIncrementCopyNumber($payout_id){
        Yii::$app->response->format = 'json';

        $payout = Payout::findOne($payout_id);
        $payout->incrementNumberCopy();

        return [
            'status' => 'sucess'
        ];
    }

}
