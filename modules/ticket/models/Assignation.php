<?php

namespace app\modules\ticket\models;

use Yii;
use app\components\db\ActiveRecord;

/**
 * This is the model class for table "assignation".
 *
 * @property integer $assignation_id
 * @property string $date
 * @property string $time
 * @property string $user_id
 * @property integer $ticket_id
 * @property integer $external_id
 *
 * @property Ticket $ticket
 */

class Assignation extends ActiveRecord {

    public $userModelClass;
    public $userModelId;

    public function init() {

        parent::init();

        $agendaModule = Yii::$app->getModule('agenda');

        if (isset($agendaModule->params['user']['class']))
            $this->userModelClass = $agendaModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($agendaModule->params['user']['idAttribute']))
            $this->userModelId = $agendaModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'assignation';
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
            [['date', 'time', 'user_id', 'ticket_id'], 'required'],
            [['date', 'time', 'ticket'], 'safe'],
            [['ticket_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'assignation_id' => Yii::t('app', 'Assignation ID'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'user_id' => Yii::t('app', 'User ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'ticket' => Yii::t('app', 'Ticket'),
            'external_id' => Yii::t('app', 'External Id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket() {
        return $this->hasOne(Ticket::className(), ['ticket_id' => 'ticket_id']);
    }
    
    public function getUser(){        
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasOne($userModel::className(), [$userPK => 'user_id']);
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

}
