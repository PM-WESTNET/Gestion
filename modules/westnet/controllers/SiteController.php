<?php

namespace app\modules\westnet\controllers;

use app\components\web\Controller;

class SiteController extends Controller {

    /**
     * inheritdoc
     */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * inheritdoc
     */
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

    /**
     * Main Ecopago Frontend view
     * @return type
     */
    public function actionIndex() {

        
        
    }

}
