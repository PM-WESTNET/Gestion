<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Customer;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\components\BaseController;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Payout;
use app\modules\westnet\mesa\components\models\Ticket;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CustomerController extends BaseController {

    /**
     * Lists all payouts from the given customer_id
     */
    public function actionPayoutHistory($id) {

        //$this->layout = '@app/views/layouts/main_no_container';

        $customer = $this->findModel($id);

        $this->redirect(Url::to([
                    'ticket/index',
                    'TicketSearch[customer]' => $customer->name,
                    'TicketSearch[document]' => $customer->document_number,
        ]));
    }

    /**
     * Returns customer information on ajax calls
     * @param type $id
     */
    public function actionGetCustomerInfo($code) {

        if (Yii::$app->request->isAjax) {

            $model = Customer::find()
                            ->orWhere(['customer.code' => $code])
                            ->orWhere(['customer.payment_code' => $code])->one();

            $json = [];
            if (!empty($model)) {

                //Find (if exist) this customer's last payout from Ecopago
                $lastPayment = Payout::find()
                        ->where(['customer_id' => $model->customer_id])
                        ->orderBy(['payout_id' => SORT_DESC])
                        ->one();
                

                if (!empty($lastPayment) && !Payout::validatePayout($lastPayment->customer_number)) {
                    if (strtotime($lastPayment->date) === strtotime(date('Y-m-d'))) {
                        $json['status'] = 'error';
                        $json['message'] = EcopagosModule::t('app', 'Two payment for same customer are not allowed in same box');
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $json;
                    }
                }
                $payment = new Payment();
                $payment->customer_id = $model->customer_id;

                $due = $payment->accountTotal();
                $due = round(abs($due < 0 ? $due : 0 ), 2);

                $json['status'] = 'success';
                $json['due'] = $due;
                $json['html'] = $this->renderAjax('customer_info', [
                    'model' => $model,
                    'lastPayment' => $lastPayment,
                    'due' => $due
                ]);
            } else {
                $json['status'] = 'error';
                $json['message'] = EcopagosModule::t('app', 'Could not found customer information with that number');
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
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
    protected function findModel($code) {

        $model = Customer::find()->where(['code' => $code])->one();

        if (!empty($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function getCustomerDebt($code){
        $info= \yii\helpers\Json::decode($this->actionGetCustomerInfo($code));
        
        return $info['due'];
    }

}
