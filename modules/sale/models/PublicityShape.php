<?php

namespace app\modules\sale\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "publicity_shape".
 *
 * @property int $publicity_shape_id
 * @property string $name
 * @property string $slug
 * @property string $status
 */
class PublicityShape extends \yii\db\ActiveRecord
{

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'publicity_shape';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'status'], 'string', 'max' => 255],
            [['name', 'slug', 'status'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'publicity_shape_id' => Yii::t('app', 'Publicity Shape ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function behaviors()
    {
        return [
            'slug'=>[
                'class' => SluggableBehavior::class,
                'slugAttribute' => 'slug',
                'attribute' => 'name',
                'ensureUnique' => true,
                'immutable' => true,
            ],
        ];
    }

    /**
     * Indica si el modelo puede eliminarse
     */
    public function getDeletable()
    {
        if(Customer::find()->where(['publicity_shape' => $this->slug])->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Devuelve un array con los estados posibles para ser deplegados en un selector
     */
    public static function getStatusForSelect()
    {
        return [
            self::STATUS_ENABLED => Yii::t('app', self::STATUS_ENABLED),
            self::STATUS_DISABLED => Yii::t('app', self::STATUS_DISABLED)
        ];
    }

    /**
     * Devuelve un array con los canales de puclicidad disponibles y habilitados
     */
    public static function  getPublicityShapeForSelect()
    {
        return ArrayHelper::map(PublicityShape::find()->where(['status' => self::STATUS_ENABLED])->all(), 'slug', 'name');
    }
}
