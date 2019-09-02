<?php

namespace app\modules\westnet\models;

use app\modules\sale\modules\contract\models\Contract;
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
            [['payment_method_id', 'created_at', 'customer_id', 'contract_id'], 'integer'],
            [['image_receipt'], 'string'],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['payment_method_id' => 'payment_method_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::class, 'targetAttribute' => ['contract_id' => 'contract_id']],
            [['amount', 'payment_method_id', 'date', 'customer_id', 'contract_id'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notify_payment_id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'amount' => Yii::t('app', 'Amount'),
            'payment_method_id' => Yii::t('app', 'Payment Method'),
            'image_receipt' => Yii::t('app', 'Image Receipt'),
            'created_at' => Yii::t('app', 'Created At'),
            'customer_id' => Yii::t('app', 'Customer'),
            'contract_id' => Yii::t('app', 'Contract'),
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
        return $this->hasOne(PaymentMethod::class, ['payment_method_id' => 'payment_method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * Sube una imagen
     * @return boolean
     */
    public function upload()
    {
        $filename = \Yii::getAlias('@webroot/uploads/payments/' . $this->image_receipt->baseName . '.' . $this->image_receipt->extension);

        if ($this->image_receipt->saveAs($filename)) {
            return true;
        }

        return false;

    }

    /**
     * Obtiene la url de la imagen
     */
    public function getUrlImage()
    {
        $url = null;
        if ($this->image_receipt !== null) {
            $url = \Yii::getAlias('uploads/payments/' . $this->image_receipt->baseName . '.' . $this->image_receipt->extension);
        }
        return $url;
    }

}