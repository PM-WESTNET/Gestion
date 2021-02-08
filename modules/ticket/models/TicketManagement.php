<?php

namespace app\modules\ticket\models;

use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\ticket\models\Ticket;

/**
 * This is the model class for table "arya_ticket.ticket_management".
 *
 * @property int $ticket_management_id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $date
 */
class TicketManagement extends \yii\db\ActiveRecord
{
    public $observation_id;

    public static function tableName()
    {
        return 'ticket_management';
    }

    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id'], 'required'],
            [['ticket_id', 'user_id', 'observation_id'], 'integer'],
            [['timestamp'], 'string', 'max' => 255],
            [['by_wp', 'by_sms', 'by_email', 'by_call'], 'boolean']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['ticket_id' => 'ticket_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ticket_management_id' => Yii::t('app', 'Ticket Management ID'),
            'ticket_id' => Yii::t('app', 'Ticket'),
            'user_id' => Yii::t('app', 'User'),
            'timestamp' => Yii::t('app', 'Date'),
            'by_wp' => Yii::t('app','WhatsApp'),
            'by_sms' => Yii::t('app', 'SMS'),
            'by_email' => Yii::t('app', 'Email'),
            'by_call' => Yii::t('app', 'Call')
        ];
    }

    /**
     * Asocia la gestion del ticket a una observación
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if($this->observation_id) {
                $observation = Observation::findOne($this->observation_id);
                if($observation) {
                    $observation->updateAttributes(['ticket_management_id' => $this->ticket_management_id]);
                }
            }
        }
    }
}
