<?php

namespace app\modules\westnet\ecopagos\frontend\helpers;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\Cashier;
use app\modules\westnet\ecopagos\models\DailyClosure;

/**
 * Helper for logged in user
 *
 * @author smaldonado
 */
class UserHelper {

    /**
     * Returns current logged in user
     * @return User
     */
    public static function getAuthUser() {

        $user = null;

        if (\Yii::$app->user) {
            $user = \Yii::$app->user;
        } else {
            throw new \yii\web\HttpException('403', EcopagosModule::t('app', 'Unauthorized access'));
        }

        return $user;
    }

    /**
     * Returns current cashier's ecopago branch
     * @return Ecopago
     * @throws \yii\web\HttpException
     */
    public static function getEcopago() {

        $user = self::getAuthUser();

        $cashier = Cashier::find()->where([
                    'user_id' => $user->id
                ])->one();

        if (empty($cashier) || !$cashier->isActive()) {
            throw new \yii\web\HttpException('403', EcopagosModule::t('app', 'Cannot find cashier (or an active cashier) associated to your user: ') . '[' . $user->username . ']');
        }

        return $cashier->ecopago;
    }

    /**
     * Returns current cashier from logged in user
     * @return Cashier
     */
    public static function getCashier() {

        $user = self::getAuthUser();

        $cashier = Cashier::find()->where([
                    'user_id' => $user->id
                ])->one();

        if (empty($cashier) || !$cashier->isActive()) {
            throw new \yii\web\HttpException('403', EcopagosModule::t('app', 'Cannot find cashier (or an active cashier) associated to your user: ') . '[' . $user->username . ']');
        }

        return $cashier;
    }

    /**
     * Checks if logged user is a cashier or not
     * @return boolean
     */
    public static function isCashier() {

        $user = self::getAuthUser();

        $cashier = Cashier::find()->where([
                    'user_id' => $user->id
                ])->one();

        if (!empty($cashier) && $cashier->isActive())
            return true;
        else
            return false;
    }

    /**
     * Checks if there is an open cash register for a cashier, today
     * @return boolean
     */
    public static function hasOpenCashRegister() {

        $cashRegister = DailyClosure::find()->where([
                    'status' => DailyClosure::STATUS_OPEN,
                    'cashier_id' => static::getCashier()->cashier_id,
                ])->one();
        
        if (!empty($cashRegister)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the current open cash register
     * @return DailyClosure
     * @throws \yii\web\HttpException
     */
    public static function getOpenCashRegister() {

        $cashRegister = DailyClosure::find()->where([
                    'status' => DailyClosure::STATUS_OPEN,
                    'cashier_id' => static::getCashier()->cashier_id,
                ])->one();

        if (empty($cashRegister))
            throw new \yii\web\HttpException('403', EcopagosModule::t('app', 'Could not found open cash register'));

        return $cashRegister;
    }

}
