<?php

namespace app\modules\ticket\models;

use Yii;

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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arya_ticket.ticket_management';
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
            [['ticket_id', 'user_id'], 'integer'],
            [['date'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->format('Y-m-d');
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ticket_management_id' => Yii::t('app', 'Ticket Management ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
