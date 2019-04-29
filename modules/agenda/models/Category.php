<?php

namespace app\modules\agenda\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property string $default_duration
 *
 * @property Task[] $tasks
 */
class Category extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'category';
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
    public function rules() {
        return [
            [['name', 'default_duration', 'slug'], 'required'],
            [['description'], 'string'],
            [['default_duration'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'category_id' => \app\modules\agenda\AgendaModule::t('app', 'Category ID'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'default_duration' => \app\modules\agenda\AgendaModule::t('app', 'Default Duration'),
            'tasks' => \app\modules\agenda\AgendaModule::t('app', 'Tasks'),
            'slug' => \app\modules\agenda\AgendaModule::t('app', 'slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks() {
        return $this->hasMany(Task::className(), ['category_id' => 'category_id']);
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

}
