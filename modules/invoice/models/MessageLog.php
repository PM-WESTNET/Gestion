<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "message_log".
 *
 * @property integer $message_log_id
 * @property integer $type
 * @property string $timestamp
 * @property integer $code
 * @property string $description
 */
class MessageLog extends \yii\db\ActiveRecord
{
    const MESSAGE_ERROR = 0;
    const MESSAGE_OBSERVATION = 1;
    const MESSAGE_EVENT = 2;


    public function behaviors()
    {
        return [
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbafip');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'code'], 'required'],
            [['type', 'code'], 'integer'],
            [['timestamp'], 'safe'],
            [['description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_log_id' => 'Error Log ID',
            'type' => 'Type',
            'timestamp' => 'Timestamp',
            'code' => 'Code',
            'description' => 'Description',
        ];
    }

    public function getType()
    {
        $ret = "";
        switch($this->type){
            case MessageLog::MESSAGE_ERROR:
                $ret = Yii::t("afip", "Error");
                break;
            case MessageLog::MESSAGE_OBSERVATION:
                $ret = Yii::t("afip", "Observation");
                break;
            case MessageLog::MESSAGE_EVENT:
                $ret = Yii::t("afip", "Event");
                break;
        }

        return $ret;
    }
}