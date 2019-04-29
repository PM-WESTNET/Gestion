<?php

namespace app\modules\westnet\ecopagos\frontend\components;

use Yii;
use app\components\web\Controller;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

class BaseController extends Controller {

    public $layout = 'ecopago_layout';

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            
            //Fetch logged user and checks if the cashier is a valid one
            UserHelper::getCashier();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if has an open cash register, and if this cash register is not an old one
     */
    protected function checkCashRegister() {

        if (!UserHelper::hasOpenCashRegister() || UserHelper::getOpenCashRegister()->isOld()) {
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Open cash register to process payouts'));
            $this->redirect(\yii\helpers\Url::to(['site/index']));
        }
    }

}
