<?php

namespace app\modules\westnet\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\westnet\notifications\components\siro\ApiSiro;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class SiroController extends Controller
{
    
    public function actionCheckerOfPayments()
    {
        if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();
            $payment_intentions = Yii::$app->db->createCommand(
                                               'SELECT * FROM siro_payment_intention WHERE 
                                                status != "payed" AND 
                                                createdAt >= :date_from AND 
                                                createdAt <= :date_to AND
                                                payment_id IS NULL AND
                                                company_id = :company_id
                                            ')
                                              ->bindValue('date_from', $request['date_from'])
                                              ->bindValue('date_to', $request['date_to'])
                                              ->bindValue('company_id', $request['company_id'])
                                              ->queryAll();

            $token = ApiSiro::GetTokenApi($request['company_id']);
            $list_of_payments_to_process = [];
            foreach ($payment_intentions as $key => $value) {
                if(isset($value['hash'])){
                    $search_payment_intention = ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $value['hash']));

                    if(!isset($search_payment_intention['Message']) || isset($search_payment_intention['PagoExitoso']))
                        if($search_payment_intention['PagoExitoso'])
                            $list_of_payments_to_process[] = $search_payment_intention + $value;
                }
            }

            if(!empty($list_of_payments_to_process)){
                $dataProvider = new ArrayDataProvider([
                    'allModels' => $result,
                    'pagination' => [
                        'pageSize' => 15,
                    ],
                ]);
                return $this->render('index', ['dataProvider' => $dataProvider]);
            }else
                Yii::$app->session->setFlash("success", "No se han encontrado intenciones de pago que se encuentren procesadas.");
        }

        return $this->render('index');
    }


}
