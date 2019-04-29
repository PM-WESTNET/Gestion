<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "discount_event".
 *
 * @property integer $discount_event_id
 * @property string $title
 * @property string $description
 * @property string $exp_date
 * @property string $exp_time
 * @property integer $exp_datetime
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property string $status
 *
 * @property ProductDiscount[] $productDiscounts
 */
class DiscountEvent extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discount_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exp_date', 'exp_time', 'date', 'time'], 'safe'],
            [['exp_datetime', 'timestamp'], 'integer'],
            [['status'], 'string'],
            [['title'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'discount_event_id' => Yii::t('app', 'Discount Event ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'exp_date' => Yii::t('app', 'Exp Date'),
            'exp_time' => Yii::t('app', 'Exp Time'),
            'exp_datetime' => Yii::t('app', 'Exp Datetime'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductDiscounts()
    {
        return $this->hasMany(ProductDiscount::className(), ['discount_event_id' => 'discount_event_id']);
    }
}
