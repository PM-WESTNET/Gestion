<?php

namespace app\modules\ticket\controllers;

use Yii;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\sale\models\Customer;

class CustomerController extends Controller {

    /**
     * Lists all tickets from the given customer_id
     */
    public function actionTicketHistory($id) {

        //$this->layout = '@app/views/layouts/main_no_container';

        $customer = $this->findModel($id);

        $this->redirect(\yii\helpers\Url::to([
                    'ticket/index',
                    'TicketSearch[customer]' => $customer->name,
                    'TicketSearch[document]' => $customer->document_number,
        ]));
    }

    /**
     * Returns customer information on ajax calls
     * @param type $id
     */
    public function actionGetCustomerInfo($id) {

        if (\Yii::$app->request->isAjax) {
            
            $model = $this->findModel($id);
                                   
            $json = [];
            $json['status'] = 'success';
            $json['html'] = $this->renderAjax('customer_info', [
                'model' => $model,
            ]);
            
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $json;
            
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
