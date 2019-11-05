<?php

namespace app\modules\checkout\models;

use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\paycheck\models\Paycheck;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "payment_item".
 *
 * @property integer $payment_item_id
 * @property integer $payment_id
 * @property string $description
 * @property string $number
 * @property double $amount
 * @property integer $payment_method_id
 * @property integer $paycheck_id
 * @property integer $money_box_account_id
 * @property integer $user_id
 *
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property Paycheck $paycheck
 * @property Payment $payment
 * @property PaymentMethod $paymentMethod
 * @property User $user
 */
class PaymentItem extends \app\components\db\ActiveRecord
{

    public $money_box_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_item';
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
            [['payment_id', 'payment_method_id', 'amount'], 'required'],
            [['payment_id', 'payment_method_id', 'paycheck_id', 'money_box_account_id', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['moneyBoxAccount', 'paycheck', 'payment', 'paymentMethod'], 'safe'],
            [['description'], 'string', 'max' => 150],
            [['number'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_item_id' => Yii::t('app', 'Payment Item ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'description' => Yii::t('app', 'Description'),
            'number' => Yii::t('app', 'Number'),
            'amount' => Yii::t('app', 'Amount'),
            'payment_method_id' => Yii::t('app', 'Payment Method ID'),
            'paycheck_id' => Yii::t('app', 'Paycheck ID'),
            'money_box_account_id' => Yii::t('app', 'Money Box Account ID'),
            'moneyBoxAccount' => Yii::t('app', 'MoneyBoxAccount'),
            'paycheck' => Yii::t('app', 'Paycheck'),
            'payment' => Yii::t('app', 'Payment'),
            'paymentMethod' => Yii::t('app', 'PaymentMethod'),
            'user_id' => Yii::t('app','User')
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
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['payment_method_id' => 'payment_method_id']);
    }
    
        
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
     * Weak relations: MoneyBoxAccount, Paycheck, Payment, PaymentMethod.
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

    public function beforeSave($insert)
    {
        // Registro el usuario que esta creando el item
        if ($insert && !Yii::$app->request->isConsoleRequest && !empty(Yii::$app->user->getIdentity())){
            $this->user_id = Yii::$app->user->id;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

}
