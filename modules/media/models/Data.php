<?php

namespace app\modules\media\models;

use Yii;

/**
 * This is the model class for table "data".
 *
 * @property integer $data_id
 * @property integer $media_id
 * @property string $attribute
 * @property string $type
 * @property string $value
 *
 * @property Media $media
 */
class Data extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbmedia');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['media_id'], 'required'],
            [['media_id'], 'integer'],
            [['value'], 'string'],
            [['attribute', 'type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'data_id' => Yii::t('app', 'Data ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'attribute' => Yii::t('app', 'Attribute'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['media_id' => 'media_id']);
    }
}
