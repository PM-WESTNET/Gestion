<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\Cashier;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CashierController implements the CRUD actions for Cashier model.
 */
class CashierController extends BaseController {

    /**
     * Updates an existing Cashier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionChangePassword() {

        $model = \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier();
        
        $model->scenario = Cashier::SCENARIO_CHANGE_PASSWORD;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->getSession()->setFlash('success', EcopagosModule::t('app', 'Password successfully updated!'));
            return $this->redirect(['site/index']);
        } else {

            if ($model->hasErrors())
                Yii::$app->getSession()->setFlash('error', EcopagosModule::t('app', 'An error ocurred when updating this cashier'));

            return $this->render('update', [
                        'model' => $model,
            ]);
        }
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
