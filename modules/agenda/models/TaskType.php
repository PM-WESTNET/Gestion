<?php

namespace app\modules\agenda\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "task_type".
 *
 * @property integer $task_type_id
 * @property string $name
 * @property string $description
 * @property string $slug
 *
 * @property Task[] $tasks
 */
class TaskType extends \app\components\db\ActiveRecord {

    const TYPE_GLOBAL = 'global';
    const TYPE_BY_USER = 'by_user';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'task_type';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
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
    public function rules() {
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
    public function attributeLabels() {
        return [
            'task_type_id' => \app\modules\agenda\AgendaModule::t('app', 'Task Type ID'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'slug' => \app\modules\agenda\AgendaModule::t('app', 'slug'),
            'tasks' => \app\modules\agenda\AgendaModule::t('app', 'Tasks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks() {
        return $this->hasMany(Task::className(), ['task_type_id' => 'task_type_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: Tasks.
     */
    public function getDeletable() {
        if ($this->getTasks()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
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

    public static function getForSelect()
    {
        return ArrayHelper::map(self::find()->all(), 'task_type_id', 'name');
    }
}
