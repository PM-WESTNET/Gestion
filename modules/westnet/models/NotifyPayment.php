<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\checkout\models\PaymentMethod;
use app\modules\sale\models\Customer;

/**
 * This is the model class for table "notify_payment".
 *
 * @property int $notify_payment_id
 * @property string $date
 * @property double $amount
 * @property int $payment_method_id
 * @property string $image_receipt
 * @property int $created_at
 *
 * @property PaymentMethod $paymentMethod
 */
class NotifyPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notify_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['amount'], 'number'],
            [['payment_method_id', 'created_at', 'customer_id'], 'integer'],
            [['image_receipt'], 'string'],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['payment_method_id' => 'payment_method_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['amount', 'payment_method_id', 'image_receipt', 'date', 'customer_id'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notify_payment_id' => Yii::t('app', 'Notify Payment ID'),
            'date' => Yii::t('app', 'Date'),
            'amount' => Yii::t('app', 'Amount'),
            'payment_method_id' => Yii::t('app', 'Payment Method ID'),
            'image_receipt' => Yii::t('app', 'Image Receipt'),
            'created_at' => Yii::t('app', 'Created At'),
            'customer_id' => Yii::t('app', 'Customer'),
        ];
    }

    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['payment_method_id' => 'payment_method_id']);
    }
}
