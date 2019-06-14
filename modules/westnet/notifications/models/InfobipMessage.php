<?php

namespace app\modules\westnet\notifications\models;

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
            [['sent_timestamp'], 'integer'],
            [['bulkId', 'status_description'], 'string', 'max' => 255],
            [['messageId', 'to', 'status'], 'string', 'max' => 45],
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
            'to' => Yii::t('app', 'To'),
            'status' => Yii::t('app', 'Status'),
            'status_description' => Yii::t('app', 'Status Description'),
            'sent_timestamp' => Yii::t('app', 'Sent Timestamp'),
        ];
    }
}
