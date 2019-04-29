<?php

namespace app\modules\ticket\models;

use Yii;

/**
 * This is the model class for table "history".
 *
 * @property integer $history_id
 * @property string $title
 * @property string $date
 * @property string $time
 * @property integer $datetime
 * @property integer $ticket_id
 * @property string $user_id
 *
 * @property Ticket $ticket
 */
class History extends \app\components\db\ActiveRecord {

    const TITLE_CREATED = 'created';
    const TITLE_UPDATED = 'updated';
    const TITLE_CLOSED = 'closed';
    const TITLE_REOPENED = 'reopened';
    const TITLE_NEW_OBSERVATION = 'new_observation';

    public $userModelClass;
    public $userModelId;

    /**
     * @inheritdoc
     */
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
        return 'history';
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
    /*
      public function behaviors()
      {
      return [
      'timestamp' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
      ],
      ],
      'date' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
      ],
      'value' => function(){return date('Y-m-d');},
      ],
      'time' => [
      'class' => 'yii\behaviors\TimestampBehavior',
      'attributes' => [
      yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
      ],
      'value' => function(){return date('h:i');},
      ],
      ];
      }
     */

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'ticket_id', 'user_id'], 'required'],
            [['title'], 'string'],
            [['date', 'time', 'ticket'], 'safe'],
            [['date'], 'date'],
            [['datetime', 'ticket_id'], 'integer'],
            [['user_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'history_id' => \app\modules\ticket\TicketModule::t('app', 'ID'),
            'title' => \app\modules\ticket\TicketModule::t('app', 'Title'),
            'date' => \app\modules\ticket\TicketModule::t('app', 'Date'),
            'time' => \app\modules\ticket\TicketModule::t('app', 'Time'),
            'datetime' => \app\modules\ticket\TicketModule::t('app', 'Datetime'),
            'ticket_id' => \app\modules\ticket\TicketModule::t('app', 'Ticket ID'),
            'user_id' => \app\modules\ticket\TicketModule::t('app', 'User ID'),
            'ticket' => \app\modules\ticket\TicketModule::t('app', 'Ticket'),
            'user' => \app\modules\ticket\TicketModule::t('app', 'User'),
        ];
    }

    /**
     * Returns all possible pre-existing titles for history entries
     * @return type
     */
    public static function titleLabels() {
        return [
            'created' => \app\modules\ticket\TicketModule::t('app', 'Ticket has been created'),
            'updated' => \app\modules\ticket\TicketModule::t('app', 'Ticket has been updated'),
            'closed' => \app\modules\ticket\TicketModule::t('app', 'Ticket has been closed'),
            'reopened' => \app\modules\ticket\TicketModule::t('app', 'Ticket has been reopened'),
            'new_observation' => \app\modules\ticket\TicketModule::t('app', 'New observation created'),
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getTicket() {
        return $this->hasOne(Ticket::className(), ['ticket_id' => 'ticket_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
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
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
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

    /**
     * Creates a new entry for a ticket being created/updated/closed/reopened
     * @param \app\modules\ticket\models\Ticket $ticket
     * @param type $isNewTicket
     */
    public static function createHistoryEntry(Ticket $ticket, $title = 'created') {

        $titleLabels = self::titleLabels();

        $historyEntry = new self;
        $historyEntry->ticket_id = $ticket->ticket_id;
        $historyEntry->user_id = (Yii::$app instanceof \yii\console\Application ? 1 : Yii::$app->user->id);

        $historyEntry->datetime = time();
        $historyEntry->date = date("Y-m-d");
        $historyEntry->time = date("H:i:s");

        $historyEntry->title = $titleLabels[$title];

        $historyEntry->save(false);
    }

}
