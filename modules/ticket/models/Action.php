<?php

namespace app\modules\ticket\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "action".
 *
 * @property integer $action_id
 * @property string $name
 * @property string $slug
 * @property string $type
 */
class Action extends \app\components\db\ActiveRecord
{

    const TYPE_TICKET = 'ticket';
    const TYPE_EVENT = 'event';
    const TYPE_DATA_EDITION = 'data-edition';


    public static function tableName()
    {
        return 'action';
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
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::class,
                    'slugAttribute' => 'slug',
                    'attribute' => 'name',
            ],
        ];
    }


    /**
     * @inheritdocSlug
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action_id' => Yii::t('app', 'Action'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'type' => Yii::t('app', 'Type'),
        ];
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

    /**
     * @return array
     * Devuelve un listado de acciones para ser desplegadas en un select2
     */
    public static function getForSelect()
    {
        return ArrayHelper::map(Action::find()->all(), 'action_id', 'name');
    }

    /**
     * @return array
     * Devuelve el listado de los tipos de acciones para ser listadas en un select2
     */
    public static function getTypeForSelect()
    {
        return [
            self::TYPE_EVENT => Yii::t('app', self::TYPE_EVENT),
            self::TYPE_TICKET => Yii::t('app', self::TYPE_TICKET),
        ];
    }

}
