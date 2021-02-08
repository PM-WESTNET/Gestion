<?php

namespace app\modules\westnet\notifications\modules\integratech\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use app\modules\westnet\notifications\models\IntegratechReceivedSms;

class IntegratechController extends ActiveController
{
    public $modelClass = '\app\modules\westnet\notifications\models\IntegratechReceivedSms';

    public function actions()
    {
        if (!Yii::$app->request->isConsoleRequest){
            \Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['create'], $actions['update'], $actions['delete'],  $actions['index']);

        return $actions;
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $integratech_received_sms = new IntegratechReceivedSms();
        if(array_key_exists('DESTADDR', $post)){
            $integratech_received_sms->destaddr = Yii::$app->request->post('DESTADDR');
        }
        if(array_key_exists('MESSAGE', $post)){
            $integratech_received_sms->message = Yii::$app->request->post('MESSAGE');
        }
        if(array_key_exists('CHARCODE', $post)){
            $integratech_received_sms->charcode = Yii::$app->request->post('CHARCODE');
        }
        if(array_key_exists('SOURCEADDR', $post)){
            $integratech_received_sms->sourceaddr = Yii::$app->request->post('SOURCEADDR');
        }
        $integratech_received_sms->save();
        \Yii::trace($post);
        return $integratech_received_sms->integratech_received_sms_id;
    }
}
