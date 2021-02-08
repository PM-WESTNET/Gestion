<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class TestController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        $request = \Yii::$app->request;

        if (strpos($request->serverName, 'localhost'))
            return $this->redirect(['site']);

        return $this->render('index');
    }

    public function actionAllButtons() {
        return $this->render('all-buttons');
    }

}
