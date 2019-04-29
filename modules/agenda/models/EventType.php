<?php

namespace app\modules\agenda\models;

use Yii;

/**
 * This is the model class for table "event_type".
 *
 * @property integer $event_type_id
 * @property string $name
 * @property string $description
 * @property string $slug
 *
 * @property Event[] $events
 */
class EventType extends \app\components\db\ActiveRecord
{

    const EVENT_NOTE_ADDED = 'note_added';
    const EVENT_STATUS_CHANGED = 'status_change';
    const EVENT_PRIORITY_CHANGED = 'priority_changed';
    const EVENT_TASK_CREATED = 'task_created';
    const EVENT_TASK_UPDATED = 'task_updated';
    const EVENT_DATE_CHANGED = 'date_changed';
    const EVENT_TASK_EXPIRED = 'task_expired';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_type';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbagenda');
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
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_type_id' => \app\modules\agenda\AgendaModule::t('app', 'Event Type ID'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'slug' => \app\modules\agenda\AgendaModule::t('app', 'System name'),
            'events' => \app\modules\agenda\AgendaModule::t('app', 'Events'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['event_type_id' => 'event_type_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: Events.
     */
    public function getDeletable()
    {
        if($this->getEvents()->exists()){
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
