<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * This is the model class for table "withdrawal".
 *
 * @property integer $withdrawal_id
 * @property integer $daily_closure_id
 * @property integer $cashier_id
 * @property double $amount
 * @property string $datetime
 *
 * @property Cashier $cashier
 * @property DailyClosure $dailyClosure
 */
class Withdrawal extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'withdrawal';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['amount'], 'required'],
            [['daily_closure_id', 'cashier_id'], 'integer'],
            [['amount'], 'number'],
            [['datetime', 'cashier', 'dailyClosure'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'withdrawal_id' => EcopagosModule::t('app', 'Withdrawal'),
            'daily_closure_id' => EcopagosModule::t('app', 'Daily closure'),
            'cashier_id' => EcopagosModule::t('app', 'Cashier'),
            'amount' => EcopagosModule::t('app', 'Amount'),
            'datetime' => EcopagosModule::t('app', 'Datetime'),
            'cashier' => EcopagosModule::t('app', 'Cashier'),
            'dailyClosure' => EcopagosModule::t('app', 'Daily closure'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashier() {
        return $this->hasOne(Cashier::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDailyClosure() {
        return $this->hasOne(DailyClosure::className(), ['daily_closure_id' => 'daily_closure_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Cashier, DailyClosure.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            //If this is a new instance
            if ($insert) {
                $this->datetime = time();
                $this->cashier_id = UserHelper::getCashier()->cashier_id;
                $this->daily_closure_id = UserHelper::getOpenCashRegister()->daily_closure_id;
            }
            //If this is not a new instance
            else {
                
            }            

            return true;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
