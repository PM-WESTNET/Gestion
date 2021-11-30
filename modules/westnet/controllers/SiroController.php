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
        
            foreach ($payment_intentions as $key => $value) {
                if(isset($value['hash']))
                    $search_payment_intention = ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $value['hash']));

                var_dump($search_payment_intention);die();
            }
        }
        

        //$token = ApiSiro::GetTokenApi($paymentIntention->company_id);
        //return ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $paymentIntention->hash, 'id_resultado' => $id_resultado))

        return $this->render('index');
    }


}
