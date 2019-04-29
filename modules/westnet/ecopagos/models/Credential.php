<?php

namespace app\modules\westnet\ecopagos\models;

use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use Yii;

/**
 * This is the model class for table "credential".
 *
 * @property integer $credential_id
 * @property integer $customer_id
 * @property integer $cashier_id
 * @property integer $datetime
 * @property string $status
 * @property string $customer_number
 *
 * @property Cashier $cashier
 */
class Credential extends \app\components\db\ActiveRecord {

    //Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'credential';
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
            [['customer_number'], 'required'],
            [['customer_id', 'cashier_id', 'datetime'], 'integer'],
            [['status'], 'string'],
            [['cashier'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'credential_id' => EcopagosModule::t('app', 'Credential'),
            'customer_id' => EcopagosModule::t('app', 'Customer'),
            'cashier_id' => EcopagosModule::t('app', 'Cashier'),
            'datetime' => EcopagosModule::t('app', 'Datetime'),
            'status' => EcopagosModule::t('app', 'Status'),
            'cashier' => EcopagosModule::t('app', 'Cashier'),
            'customer_number' => EcopagosModule::t('app', 'Customer number'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public function fetchStatuses() {
        return [
            static::STATUS_PENDING => EcopagosModule::t('app', 'Pending'),
            static::STATUS_IN_PROGRESS => EcopagosModule::t('app', 'In progress'),
            static::STATUS_COMPLETED => EcopagosModule::t('app', 'Completed'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public static function staticFetchStatuses() {
        return [
            static::STATUS_PENDING => EcopagosModule::t('app', 'Pending'),
            static::STATUS_IN_PROGRESS => EcopagosModule::t('app', 'In progress'),
            static::STATUS_COMPLETED => EcopagosModule::t('app', 'Completed'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(\app\modules\sale\models\Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashier() {
        return $this->hasOne(Cashier::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            //Fetch customer from customer_number
            $customer = \app\modules\sale\models\Customer::find()
                    ->where(['code' => $this->customer_number])
                    ->one();
            
            if(empty($customer)){                
                return false;
            }

            //If this is a new record
            if ($insert) {
                $this->datetime = time();
                $this->cashier_id = UserHelper::getCashier()->cashier_id;
                $this->customer_id = $customer->customer_id;
                $this->status = static::STATUS_PENDING;
            }

            return true;
        }
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Cashier.
     */
    protected function unlinkWeakRelations() {
        
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
