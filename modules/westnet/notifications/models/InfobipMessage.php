<?php

namespace app\modules\westnet\notifications\models;

use app\modules\sale\models\Customer;
use Yii;

/**
 * This is the model class for table "infobip_message".
 *
 * @property int $infobip_message_id
 * @property string $bulkId
 * @property string $messageId
 * @property string $to
 * @property string $status
 * @property string $status_description
 * @property int $sent_timestamp
 * @property string $message
 * @property integer $customer_id
 *
 * @property Customer $customer
 */
class InfobipMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infobip_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bulkId', 'messageId', 'to', 'status', 'sent_timestamp'], 'required'],
            [['sent_timestamp', 'customer_id'], 'integer'],
            [['bulkId', 'status_description'], 'string', 'max' => 255],
            [['messageId', 'to', 'status'], 'string', 'max' => 45],
            [['message'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'infobip_message_id' => Yii::t('app', 'Infobip Message ID'),
            'bulkId' => Yii::t('app', 'Bulk ID'),
            'messageId' => Yii::t('app', 'Message ID'),
            'to' => Yii::t('app', 'Destinatary'),
            'status' => Yii::t('app', 'Status'),
            'status_description' => Yii::t('app', 'Status Description'),
            'sent_timestamp' => Yii::t('app', 'Sent Date and Time'),
            'customer_id' => Yii::t('app','Customer')
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }
}
