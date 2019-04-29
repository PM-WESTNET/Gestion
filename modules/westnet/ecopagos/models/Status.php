<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "status".
 *
 * @property integer $status_id
 * @property string $name
 * @property string $description
 * @property string $slug
 *
 * @property Ecopago[] $ecopagos
 */
class Status extends \app\components\db\ActiveRecord {

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
        return Yii::$app->get('dbecopago');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => 'yii\behaviors\SluggableBehavior',
                'attribute' => 'name',
                'ensureUnique' => true
            ],
        ];
    }

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
            'status_id' => Yii::t('app', 'Status ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'slug' => Yii::t('app', 'Slug'),
            'ecopagos' => Yii::t('app', 'Ecopagos'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopagos() {
        return $this->hasMany(Ecopago::className(), ['status_id' => 'status_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: Ecopagos.
     */
    public function getDeletable() {
        if ($this->getEcopagos()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
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

    /**
     * @return array
     * Devuelve un array con id => nombre del estado
     */
    public static function getStatusesForSelect()
    {
        $status_array = ArrayHelper::map(Status::find()->all(), 'status_id', 'name');
        foreach($status_array as $key => $value){
            $status_array[$key] = Yii::t('app',$value);
        }
        return $status_array;


    }

}
