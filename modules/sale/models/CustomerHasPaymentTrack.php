<?php

namespace app\modules\sale\models;

use app\modules\log\db\ActiveRecord;
use Yii;
use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\Track;

/**
 * This is the model class for table "customer_has_payment_track".
 *
 * @property int $customer_has_payment_track
 * @property int $customer_id
 * @property int $payment_method_id
 * @property int $track_id
 *
 * @property Customer $customer
 * @property PaymentMethod $paymentMethod
 * @property Track $track
 */
class CustomerHasPaymentTrack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_has_payment_track';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'payment_method_id', 'track_id'], 'required'],
            [['customer_id', 'payment_method_id', 'track_id'], 'integer'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['payment_method_id' => 'payment_method_id']],
            [['track_id'], 'exist', 'skipOnError' => true, 'targetClass' => Track::class, 'targetAttribute' => ['track_id' => 'track_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_has_payment_track' => Yii::t('app', 'Customer Has Payment Track'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'payment_method_id' => Yii::t('app', 'Payment Method ID'),
            'track_id' => Yii::t('app', 'Track ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
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
    public function getTrack()
    {
        return $this->hasOne(Track::class, ['track_id' => 'track_id']);
    }
}
