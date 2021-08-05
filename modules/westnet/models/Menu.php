<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "notify_payment".
 *
 * @property int $menu_id
 * @property string $description
 * @property string $icon
 * @property string $route
 * @property int $menu_idmenu
 * @property int $status
 * @property int $is_submenu
 * @property date created_at
 * @property date updated_at
 *
 */
class Menu extends \yii\db\ActiveRecord
{


    public static function tableName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_id', 'menu_idmenu', 'status', 'is_submenu', 'created_at', 'updated_at'], 'integer'],
            [['description','icon', 'route'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'icon' => Yii::t('app', 'Icon'),
            'route' => Yii::t('app', 'Route'),
            'menu_idmenu' => Yii::t('app','Menu ID'),
            'status' => Yii::t('app','Status'),
            'is_submenu' => Yii::t('app','Is Submenu'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app','Updated At'),
        ];
    }

    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            'updated_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                ],
            ],
        ];
    }
}
