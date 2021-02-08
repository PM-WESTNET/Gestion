<?php

namespace app\modules\provider\models;

use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\paycheck\models\Paycheck;
use Yii;

/**
 * This is the model class for table "provider_payment_item".
 *
 * @property integer $provider_payment_item_id
 * @property integer $provider_payment_id
 * @property string $description
 * @property string $number
 * @property double $amount
 * @property integer $payment_method_id
 * @property integer $paycheck_id
 * @property integer $money_box_account_id
 *
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property Paycheck $paycheck
 * @property PaymentMethod $paymentMethod
 * @property ProviderPayment $providerPayment
 */
class ProviderPaymentItem extends \app\components\db\ActiveRecord
{
    public $money_box_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_payment_item';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_payment_id'], 'required'],
            [['provider_payment_id', 'payment_method_id', 'paycheck_id', 'money_box_account_id'], 'integer'],
            [['amount'], 'number'],
            [['moneyBoxAccount', 'paycheck', 'paymentMethod', 'providerPayment'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['number'], 'string', 'max' => 45],
            ['money_box_account_id', 'required', 'when' => function($model){
                return ($model->payment_method_id == Config::getValue('payment_method_cash'));
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_payment_item_id' => Yii::t('app', 'Provider Payment Item ID'),
            'provider_payment_id' => Yii::t('app', 'Provider Payment ID'),
            'description' => Yii::t('app', 'Description'),
            'number' => Yii::t('app', 'Number'),
            'amount' => Yii::t('app', 'Amount'),
            'payment_method_id' => Yii::t('app', 'Payment Method'),
            'paycheck_id' => Yii::t('app', 'Paycheck ID'),
            'money_box_account_id' => Yii::t('app', 'Money Box Account ID'),
            'moneyBoxAccount' => Yii::t('app', 'Money Box Account'),
            'paycheck' => Yii::t('app', 'Paycheck'),
            'paymentMethod' => Yii::t('app', 'Payment Method'),
            'providerPayment' => Yii::t('app', 'Provider Payment'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaycheck()
    {
        return $this->hasOne(Paycheck::className(), ['paycheck_id' => 'paycheck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['payment_method_id' => 'payment_method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderPayment()
    {
        return $this->hasOne(ProviderPayment::className(), ['provider_payment_id' => 'provider_payment_id']);
    }
    
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBoxAccount, Paycheck, PaymentMethod, ProviderPayment.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
