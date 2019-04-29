<?php

namespace app\modules\checkout\models;

use app\modules\accounting\components\CountableInterface;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payment_receipt".
 *
 * @property integer $payment_receipt_id
 * @property double $amount
 * @property string $date
 * @property string $time
 * @property string $concept
 *
 * @property PaymentReceiptHasPayment[] $paymentReceiptHasPayments
 * @property Payment[] $payments
 */
class PaymentReceipt extends \app\components\companies\ActiveRecord implements CountableInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_receipt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'payment_method_id'], 'number'],
            [['date', 'time'], 'safe'],
            [['concept'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_receipt_id' => Yii::t('app', 'ID'),
            'amount' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'concept' => Yii::t('app', 'Concept'),
            'customer' => Yii::t('app', 'Customer'),
            'paymentMethod' => Yii::t('app', 'Payment Method')
        ];
    }

    public function behaviors()
    {
        return [
            'datestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function() {return date('Y-m-d');},
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ]
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\app\modules\sale\models\Customer::className(), ['customer_id' => 'customer_id']);
    }
    
    /** 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getPaymentMethod() 
    { 
        return $this->hasOne(PaymentMethod::className(), ['payment_method_id' => 'payment_method_id']); 
    }
    
    public function getDeletable(){
    
        return true;
        
    }

    public function getConfig()
    {
        $query = PaymentMethod::find();
        $query->select(['payment_method_id', 'name']);
        $paymentMethods = ArrayHelper::map($query->asArray()->all(), 'payment_method_id', 'name');
        $paymentMethods['total'] = 'Total';

        return $paymentMethods;
    }

    public function getAmounts()
    {
        $paymentMethods = $this->getConfig();
        foreach($paymentMethods as $key=>$value) {
            if($key == $this->payment_method_id) {
                $paymentMethods[$key] = $this->amount;
            } else {
                $paymentMethods[$key] = 0;
            }
        }
        $paymentMethods['total'] = $this->amount;
        return $paymentMethods;
    }
}
