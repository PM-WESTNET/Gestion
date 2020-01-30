<?php

namespace app\controllers;

use app\modules\checkout\models\search\PaymentSearch;
use app\modules\westnet\models\NotifyPayment;
use Yii;
use yii\filters\AccessControl;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    public function actions()
    {
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

    public function actionIndex()
    {
        //Si posee el rol, el  index debe ser la vista de agenda
        if(!Yii::$app->user->isGuest){
            if (Yii::$app->user->identity->hasRole('home_is_agenda', false)) {
                return $this->redirect(['/agenda/default/index']);
            }


            if(Yii::$app->user->identity->hasRole('User-alert-new-no-verified-tranferences', false) && NotifyPayment::transferenceNotifyPaymentsNotVerifiedExists()) {
                Yii::$app->session->addFlash('info', Yii::t('app', 'Theres one or more notify payments by transference not verified'));
            }
        }
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        $paymentSearch = new PaymentSearch();
        $paymentSearch->customer_id = 6;

        $debt = round((float)$paymentSearch->accountTotal(), 2);

        var_dump($debt);
        Yii::trace($debt);
        die();
        return $this->render('about');
    }
}
