<?php

namespace app\modules\media\models;

use Yii;

/**
 * This is the model class for table "data".
 *
 * @property integer $sized_id
 * @property integer $media_id
 * @property string $width
 * @property string $height
 * @property string $relative_url
 * @property string $base_url
 *
 * @property Media $media
 */
class Sized extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sized';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sized_id' => Yii::t('app', 'Sized ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['media_id' => 'media_id']);
    }
    
    public function getUrl()
    {
        return $this->base_url.'/'.$this->relative_url;
    }
    
    public function beforeSave($insert) 
    {
        $this->base_url = Yii::getAlias('@web');
        
        return parent::beforeSave($insert);
    }
}
