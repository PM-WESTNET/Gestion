<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "infobip_response".
 *
 * @property int $infobip_response_id
 * @property string $from
 * @property string $to
 * @property string $content
 * @property string $keyword
 * @property int $received_timestamp
 */
class InfobipResponse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infobip_response';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from', 'to', 'received_timestamp'], 'required'],
            [['content'], 'string'],
            [['received_timestamp'], 'integer'],
            [['from', 'to', 'keyword'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'infobip_response_id' => Yii::t('app', 'Infobip Response ID'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'content' => Yii::t('app', 'Content'),
            'keyword' => Yii::t('app', 'Keyword'),
            'received_timestamp' => Yii::t('app', 'Received Timestamp'),
        ];
    }
}
