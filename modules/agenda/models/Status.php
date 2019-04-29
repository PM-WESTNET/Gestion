<?php

namespace app\modules\agenda\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "status".
 *
 * @property integer $status_id
 * @property string $name
 * @property string $description
 * @property string $color
 * @property string $slug
 *
 * @property Task[] $tasks
 */
class Status extends \app\components\db\ActiveRecord {

    const STATUS_COMPLETED = 'completed';
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'status';
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
            [['description', 'color'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'status_id' => \app\modules\agenda\AgendaModule::t('app', 'Status ID'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'color' => \app\modules\agenda\AgendaModule::t('app', 'Color'),
            'slug' => \app\modules\agenda\AgendaModule::t('app', 'System name'),
            'tasks' => \app\modules\agenda\AgendaModule::t('app', 'Tasks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks() {
        return $this->hasMany(Task::className(), ['status_id' => 'status_id']);
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
        return ArrayHelper::map(self::find()->all(), 'status_id', 'name');
    }

}
