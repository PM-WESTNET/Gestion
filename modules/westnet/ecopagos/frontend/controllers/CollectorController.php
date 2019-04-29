<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use app\modules\westnet\ecopagos\models\Collector;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

class CollectorController extends BaseController {

    /**
     * Returns customer information on ajax calls
     * @param type $id
     */
    public function actionGetCollectorInfo() {

        if (\Yii::$app->request->isAjax) {

            $dummyCollector = new Collector();
            $dummyCollector->load(Yii::$app->request->post());
            $collector = $dummyCollector->isValid();

            //Json response
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $json = [];

            //Valid collector
            if (!empty($collector)) {
                $model = $collector;

                //Collector assigned to this ecopago
                if ($collector->isFromEcopago(UserHelper::getCashier()->ecopago_id)) {
                    $json['status'] = 'success';
                    $json['html'] = $this->renderPartial('collector_info', [
                        'model' => $model,
                    ]);
                    
                //Collector not assigned to this ecopago
                } else {
                    $json['status'] = 'error';
                    $json['message'] = \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Collector is not from this Ecopago branch');
                }

            //Invalid collector
            } else {
                $json['status'] = 'error';
                $json['message'] = \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Invalid number or password');
            }


            return $json;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Collector model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Collector::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
