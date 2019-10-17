<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\sale\models\Customer;

/**
 * This is the model class for table "payment_extension_history".
 *
 * @property int $payment_extension_history_id
 * @property string $from
 * @property int $customer_id
 * @property string $date
 * @property int $created_at
 *
 * @property Customer $customer
 */
class PaymentExtensionHistory extends \yii\db\ActiveRecord
{
    const FROM_APP = 'app';
    const FROM_IVR = 'ivr';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_extension_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from', 'customer_id'], 'required'],
            [['from'], 'string'],
            [['customer_id', 'created_at'], 'integer'],
            [['date'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
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
                'value' => function(){
                    return (new \DateTime('now'))->getTimestamp();
                }
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->format('Y-m-d');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_extension_history_id' => Yii::t('app', 'Payment Extension History ID'),
            'from' => Yii::t('app', 'From'),
            'customer_id' => Yii::t('app', 'Customer'),
            'date' => Yii::t('app', 'Date'),
            'created_at' => Yii::t('app', 'Created At'),
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
     * Crea un registro para mantener un historial
     */
    public static function createPaymentExtensionHistory($customer_id, $from)
    {
        $model = new PaymentExtensionHistory([
            'customer_id' => $customer_id,
            'from' => $from
        ]);

        return $model->save();
    }

    /**
     * Devuelve los tipos para ser desplegados en un select
     */
    public static function getFromTypesForSelect()
    {
        return [
            PaymentExtensionHistory::FROM_APP => Yii::t('app', PaymentExtensionHistory::FROM_APP),
            PaymentExtensionHistory::FROM_IVR => Yii::t('app', PaymentExtensionHistory::FROM_IVR)
        ];
    }
}
