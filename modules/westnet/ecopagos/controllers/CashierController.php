<?php

namespace app\modules\westnet\ecopagos\controllers;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\Cashier;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CashierController implements the CRUD actions for Cashier model.
 */
class CashierController extends Controller {

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
     * Lists all Cashier models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Cashier::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cashier model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cashier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Cashier();
        $model->scenario = Cashier::SCENARIO_CREATE;
        $model->status = Cashier::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Cashier successfully created!'));
            return $this->redirect(['view', 'id' => $model->cashier_id]);
        } else {

            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when creating a cashier'));


            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Cashier for an specific Ecopago instance via $ecopago_id
     * @param int $ecopago_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAddCashier($ecopago_id) {

        $ecopago = \app\modules\westnet\ecopagos\models\Ecopago::findOne($ecopago_id);

        if (!empty($ecopago)) {

            $model = new Cashier();
            $model->ecopago_id = $ecopago->ecopago_id;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Cashier added successfully!'));
                return $this->redirect(['cashier/list-by-ecopago', 'ecopago_id' => $ecopago->ecopago_id]);
            } else {

                if ($model->hasErrors())
                    Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when creating a cashier'));

                return $this->render('add_cashier', [
                            'model' => $model,
                            'ecopago' => $ecopago
                ]);
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * List all cashiers from an specific Ecopago instance via $ecopago_id
     * @param int $ecopago_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionListByEcopago($ecopago_id) {

        $ecopago = \app\modules\westnet\ecopagos\models\Ecopago::findOne($ecopago_id);

        if (!empty($ecopago)) {

            $dataProvider = new ActiveDataProvider([
                'query' => Cashier::find()->where([
                    'ecopago_id' => $ecopago_id
                ]),
            ]);

            return $this->render('list_by_ecopago', [
                        'ecopago' => $ecopago,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Updates an existing Cashier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Cashier successfully updated!'));
            return $this->redirect(['view', 'id' => $model->cashier_id]);
        } else {

            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when updating this cashier'));

            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cashier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cashier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cashier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Cashier::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
