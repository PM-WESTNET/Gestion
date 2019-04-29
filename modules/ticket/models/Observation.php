<?php

namespace app\modules\ticket\models;

use Yii;
use app\modules\ticket\models\History;

/**
 * This is the model class for table "observation".
 *
 * @property integer $observation_id
 * @property integer $ticket_id
 * @property string $user_id
 * @property integer $order
 * @property string $title
 * @property string $description
 * @property string $date
 * @property string $time
 * @property integer $datetime
 *
 * @property Ticket $ticket
 */
class Observation extends \app\components\db\ActiveRecord {

    public $userModelClass;
    public $userModelId;

    public function init() {

        parent::init();

        $ticketModule = Yii::$app->getModule('ticket');

        if (isset($ticketModule->params['user']['class']))
            $this->userModelClass = $ticketModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($ticketModule->params['user']['idAttribute']))
            $this->userModelId = $ticketModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'observation';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ticket_id', 'user_id', 'title', 'description'], 'required'],
            [['ticket_id', 'user_id', 'order'], 'integer'],
            [['description'], 'string'],
            [['date', 'time', 'ticket', 'order'], 'safe'],
            [['date'], 'date'],
            [['title'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'observation_id' => \app\modules\ticket\TicketModule::t('app', 'Observation ID'),
            'ticket_id' => \app\modules\ticket\TicketModule::t('app', 'Ticket ID'),
            'user_id' => \app\modules\ticket\TicketModule::t('app', 'User ID'),
            'title' => \app\modules\ticket\TicketModule::t('app', 'Title'),
            'description' => \app\modules\ticket\TicketModule::t('app', 'Description'),
            'date' => \app\modules\ticket\TicketModule::t('app', 'Date'),
            'time' => \app\modules\ticket\TicketModule::t('app', 'Time'),
            'ticket' => \app\modules\ticket\TicketModule::t('app', 'Ticket'),
            'order' => \app\modules\ticket\TicketModule::t('app', 'Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket() {
        return $this->hasOne(Ticket::className(), ['ticket_id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {

        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;

        return $this->hasOne($userModel::className(), [$userPK => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->order = $this->fetchOrder();
            }
            $this->datetime = time();
            if(!$this->time) {
                $this->time = date("H:i");
            }

            $this->formatDatesBeforeSave();

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        History::createHistoryEntry($this->ticket, History::TITLE_NEW_OBSERVATION);
    }

    /**
     * Returns the current order for this observation
     * @return type
     */
    private function fetchOrder() {
        return count($this->ticket->observations) + 1;
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        if (!empty($this->date))
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        else
            $this->date = date('Y-m-d');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Ticket.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
