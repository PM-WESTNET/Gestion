<?php

namespace app\modules\westnet\ecopagos\controllers;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\Ecopago;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EcopagoController implements the CRUD actions for Ecopago model.
 */
class EcopagoController extends Controller {

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
     * Lists all Ecopago models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Ecopago::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ecopago model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ecopago model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Ecopago();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Ecopago created successfully!'));
            return $this->redirect(['view', 'id' => $model->ecopago_id]);
            
        } else {
            
            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when creating the Ecopago branch'));
            
            return $this->render('create', [
                        'model' => $model,
            ]);
            
        }
    }

    /**
     * Updates an existing Ecopago model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Changes saved successfully!'));
            return $this->redirect(['view', 'id' => $model->ecopago_id]);
            
        } else {
            
            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when updating the Ecopago branch'));
            
            return $this->render('update', [
                        'model' => $model,
            ]);
            
        }
    }

    /**
     * Manages cashier assignations for an Ecopago
     */
    public function actionCollectors($id) {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Changes on collectors saved successfully!'));
            return $this->redirect(['view', 'id' => $model->ecopago_id]);
        } else {

            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'Changes on collectors could not be saved'));

            return $this->render('collectors', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Ecopago model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ecopago model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ecopago the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ecopago::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDisable($id) {
        $model = $this->findModel($id);

        if ($model->disable()){
            Yii::$app->session->addFlash('success', Yii::t('app','Ecopago disabled successfull'));
        }else {
            Yii::$app->session->addFlash('error', Yii::t('app','Can`t disable this Ecopago'));
        }

        return $this->redirect(['view', 'id' => $model->ecopago_id]);
    }

}
