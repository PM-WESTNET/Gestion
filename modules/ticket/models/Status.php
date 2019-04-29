<?php

namespace app\modules\ticket\models;

use Yii;
use app\modules\ticket\TicketModule;

/**
 * This is the model class for table "status".
 *
 * @property integer $status_id
 * @property string $name
 * @property string $description
 * @property integer $is_open
 *
 * @property Ticket[] $tickets
 */
class Status extends \app\components\db\ActiveRecord
{
    const  STATUS_CLOSED= '0';
    const  STATUS_ACTIVE= '1';
    /**
     * @inheritdoc
     */
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
            [['is_open'], 'integer'],
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
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['status_id' => 'status_id']);
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
}