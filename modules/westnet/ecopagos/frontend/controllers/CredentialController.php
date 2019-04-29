<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use app\modules\westnet\ecopagos\models\Credential;
use yii\data\ActiveDataProvider;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CredentialController implements the CRUD actions for Credential model.
 */
class CredentialController extends BaseController {

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
     * Lists all Credential models.
     * @return mixed
     */
    public function actionIndex() {
        $user_id = Yii::$app->user->getId();

        $dataProvider = new ActiveDataProvider([
            'query' => Credential::find()
                        ->leftJoin('cashier', 'cashier.cashier_id = credential.cashier_id')
                        ->where(['cashier.user_id' => $user_id])
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Credential model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new credential reprint ask
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionReprintAsk() {
        $model = new Credential();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->credential_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Credential model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Credential model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Credential the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Credential::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
