<?php

namespace app\modules\westnet\notifications\models;

use Yii;
use app\modules\westnet\notifications\models\Notification;
use app\modules\westnet\notifications\models\Transport;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\components\transports\IntegratechService;

/**
 * This is the model class for table "integratech_message".
 *
 * @property integer $id
 * @property string $mensaje
 * @property string $telefono
 * @property integer $idcontacto
 * @property integer $idlogllamado
 * @property string $rediscado
 * @property string $estado
 * @property integer $idcampana
 * @property string $tipomensaje
 * @property string $imeiEquipo_gsm
 */
class IntegratechMessage extends \app\components\db\ActiveRecord
{
    CONST STATUS_PENDING = 'pending';
    CONST STATUS_SENT = 'sent';
    CONST STATUS_ERROR = 'error';
    CONST STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return 'integratech_message';
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['datetime'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['message', 'phone'], 'required'],
            [['response_message_id', 'response_status_code', 'customer_id'], 'integer'],
            [['response_status_text'], 'string'],
            [['status', 'notification_id', 'datetime'], 'safe'],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'integratech_message_id' => Yii::t('app', 'ID'),
            'message' => Yii::t('app', 'Message'),
            'phone' => Yii::t('app', 'Phone'),
            'datetime' => Yii::t('app', 'Datetime'),
            'response_message_id' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Integratech response message id'),
            'response_status_text' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Integratech response status text'),
            'response_status_code' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Integratech response status code'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbnotifications');
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
     * Weak relations: None.
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
    
    public function send(){

        $response = IntegratechService::sendSMS($this->phone, $this->message);

        $result = $response['response']['results'][0];

        if($response['status'] == 'success'){
            $this->status = IntegratechMessage::STATUS_SENT;
            $this->response_status_code = $result['status'];
            $this->response_status_text = $result['statustext'];
            $this->response_message_id = $result['msgid'];
        } else {
            $this->status = IntegratechMessage::STATUS_ERROR;
            $this->response_status_code = $result['status'];
            $this->response_status_text = $result['statustext'];
        }

        if(!$this->save()){
            return [
                'status' => 'error',
                'saving' => false,
                'response' => $response ? $response['status'] =='success' : false
            ];
        }

        return [
            'status' => 'success',
            'saving' => true,
            'response' => $response ? $response['status'] =='success' : false
        ];
    }

    public function markAsCancelled(){
        $this->status = IntegratechMessage::STATUS_CANCELLED;
        $this->save();
    }

}
