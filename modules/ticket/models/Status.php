<?php

namespace app\modules\ticket\models;

use Yii;
use app\modules\ticket\TicketModule;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "status".
 *
 * @property integer $status_id
 * @property string $name
 * @property string $description
 * @property integer $is_open
 * @property integer $generate_action
 *
 * @property Ticket[] $tickets
 */
class Status extends \app\components\db\ActiveRecord
{
    const  STATUS_CLOSED = '0';
    const  STATUS_ACTIVE = '1';

    public static function tableName()
    {
        return 'status';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbticket');
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['is_open', 'generate_action'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status_id' => TicketModule::t('app', 'Status'),
            'name' => TicketModule::t('app', 'Name'),
            'description' => TicketModule::t('app', 'Description'),
            'is_open' => TicketModule::t('app', 'Is Open'),
            'tickets' => TicketModule::t('app', 'Tickets'),
            'generate_action' => TicketModule::t('app', 'Genera una acción?'),
            'action_id' => Yii::t('app', 'Action'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Action::class, ['action_id' => 'action_id'])->viaTable('status_has_action', ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionConfig()
    {
        return $this->hasOne(StatusHasAction::class, ['status_id' => 'status_id']);
    }
             
    /**
     * @inheritdoc
     * Strong relations: Tickets.
     */
    public function getDeletable()
    {
        if($this->getTickets()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
        //Elimino las configuraciones de una accion generada que pueda llegar a tener asociada
        if($this->actionConfig) {
            $this->actionConfig->delete();
        };
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

    /**
     * @return array
     * Devuelve los estados existentes para un desplegable
     */
    public static function getForSelect()
    {
        return ArrayHelper::map(self::find()->all(), 'status_id', 'name');
    }

    /**
     * @return bool
     * Indica si el estado esta asociado a una accion de tipo event
     */
    public function hasEventAction()
    {
        if($this->action) {
            if($this->action->type == Action::TYPE_EVENT) {
                return true;
            }
        }

        return false;
    }
}