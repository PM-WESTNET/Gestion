<?php

namespace app\modules\westnet\ecopagos\helpers;

use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\westnet\ecopagos\models\BatchClosure;
use app\modules\accounting\models\MoneyBoxType;
use app\modules\accounting\models\MoneyBox;
use app\modules\config\models\Config;

/**
 * Indirection for Countable functions
 *
 * @author smaldonado
 */
class CountableHelper {

    const MOVEMENT_DESCRIPTION = 'Rendición Ecopago';

    /**
     * Returns a list for banks that can be used to render payments from Ecopagos
     * @return boolean
     */
    public static function fetchEcopagoBanks() {

        try {

            $moneyBoxes = MoneyBoxAccount::find()
                ->where(['money_box_id'=> Config::getValue('ecopago_money_box_id')])
                ->all();

            return $moneyBoxes;
        } catch (yii\base\Exception $e) {
            //If a problem happened, throw an exception for migraiton running
            Yii::$app->session->setFlash("error", \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Please run migrations and create money boxes for batch closure renders'));
            throw new \yii\web\HttpException('404', \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Please run migrations and create money boxes for batch closure renders'));
        }
    }

    /**
     * NOT USED | Creates a new countable movement
     * @param string $description
     * @param integer $company_id
     * @param array() $items
     * @param string $status
     */
    public static function createCountableMovement(BatchClosure $batchClosure) {

        //Debit movement for Ecopago account
        $debitAccountMovementItem = new AccountMovementItem();
        $debitAccountMovementItem->account_id = $batchClosure->ecopago->account_id;
        $debitAccountMovementItem->debit = $batchClosure->real_total;
        $debitAccountMovementItem->status = 'draft';
        $items[] = $debitAccountMovementItem;

        //Credit movement for Westnet account
        $creditAccountMovementItem = new AccountMovementItem();
        $creditAccountMovementItem->account_id = $batchClosure->ecopago->account_id;
        $creditAccountMovementItem->credit = $batchClosure->real_total;
        $creditAccountMovementItem->status = 'draft';
        $items[] = $creditAccountMovementItem;

        if (CountableMovement::createMovement(static::MOVEMENT_DESCRIPTION, $company_id, $items))
            return true;
        else
            return false;
    }

    /**
     * NOT USED | Returns an array with AccountMovementItem, each one builded from an array structure
     * @param array $movementItemStructures
     * @return array AccountMovementItem
     */
    private static function buildAccountMovementItems($movementItemStructures = []) {

        $accountMovementItems = [];

        if (!empty($movementItemStructures)) {

            foreach ($movementItemStructures as $movementItemStructure) {

                $accountMovementItem = new AccountMovementItem();
                $accountMovementItem->load($movementItemStructure);
                $accountMovementItems[] = $accountMovementItem;
            }
        }

        return $accountMovementItems;
    }

}
