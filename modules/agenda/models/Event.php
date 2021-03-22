<?php

namespace app\modules\agenda\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property integer $event_id
 * @property integer $task_id
 * @property integer $user_id
 * @property integer $event_type_id
 * @property string $date
 * @property string $time
 * @property string $datetime
 *
 * @property EventType $eventType
 * @property Task $task
 * @property User $user
 */
class Event extends \app\components\db\ActiveRecord
{
    
    public $userModelClass;
    public $userModelId;

    public function init() {
        parent::init();
        if (isset(Yii::$app->modules['agenda']->params['user']['class']))
            $this->userModelClass = Yii::$app->modules['agenda']->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset(Yii::$app->modules['agenda']->params['user']['idAttribute']))
            $this->userModelId = Yii::$app->modules['agenda']->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
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
            [['task_id', 'user_id', 'event_type_id'], 'required'],
            [['task_id', 'user_id', 'event_type_id'], 'integer'],
            [['date', 'time', 'datetime', 'eventType', 'task'], 'safe'],
            [['date'], 'date']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => \app\modules\agenda\AgendaModule::t('app', 'Event ID'),
            'task_id' => \app\modules\agenda\AgendaModule::t('app', 'Task ID'),
            'user_id' => \app\modules\agenda\AgendaModule::t('app', 'User ID'),
            'event_type_id' => \app\modules\agenda\AgendaModule::t('app', 'Event Type ID'),
            'date' => \app\modules\agenda\AgendaModule::t('app', 'Date'),
            'time' => \app\modules\agenda\AgendaModule::t('app', 'Time'),
            'datetime' => \app\modules\agenda\AgendaModule::t('app', 'Datetime'),
            'eventType' => \app\modules\agenda\AgendaModule::t('app', 'EventType'),
            'task' => \app\modules\agenda\AgendaModule::t('app', 'Task'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventType()
    {
        return $this->hasOne(EventType::className(), ['event_type_id' => 'event_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['task_id' => 'task_id']);
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
     
    public function beforeSave($insert)
    {
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
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
            $this->date = Yii::$app->formatter->asDate($this->date);
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
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
     * Weak relations: EventType, Task.
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
