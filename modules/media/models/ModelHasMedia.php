<?php

namespace app\modules\media\models;

use Yii;

/**
 * This is the model class for table "model_has_media".
 *
 * @property integer $media_id
 * @property integer $model_id
 * @property string $model
 * @property integer $order
 *
 * @property Media $media
 */
class ModelHasMedia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'model_has_media';
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
            [['media_id', 'model_id'], 'required'],
            [['media_id', 'model_id', 'order'], 'integer'],
            [['model'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => Yii::t('app', 'Media ID'),
            'model_id' => Yii::t('app', 'Model ID'),
            'model' => Yii::t('app', 'Model'),
            'order' => Yii::t('app', 'Order'),
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
