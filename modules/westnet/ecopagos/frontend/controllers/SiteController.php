<?php

namespace app\modules\westnet\ecopagos\frontend\controllers;

use app\modules\westnet\ecopagos\frontend\components\BaseController;

class SiteController extends BaseController {

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

        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'empty';
        return $this->render('index');
    }
    
    public function actionPrintInstructions(){
        
        $this->layout= 'empty';
        
        return $this->render('print-instructions');
    }

}
