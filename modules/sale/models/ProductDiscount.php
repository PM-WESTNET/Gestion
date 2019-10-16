<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "product_discount".
 *
 * @property integer $product_discount_id
 * @property double $qty
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property string $exp_date
 * @property string $exp_time
 * @property integer $exp_timestamp
 * @property string $description
 * @property integer $product_id
 * @property integer $discount_event_id
 * @property string $status
 * @property double $limit
 *
 * @property Product $product
 * @property DiscountEvent $discountEvent
 */
class ProductDiscount extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qty', 'limit'], 'number'],
            [['date', 'time', 'exp_date', 'exp_time'], 'safe'],
            [['timestamp', 'exp_timestamp', 'product_id', 'discount_event_id'], 'integer'],
            [['product_id'], 'required'],
            [['status'], 'string'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_discount_id' => Yii::t('app', 'Product Discount ID'),
            'qty' => Yii::t('app', 'Qty'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'exp_date' => Yii::t('app', 'Exp Date'),
            'exp_time' => Yii::t('app', 'Exp Time'),
            'exp_timestamp' => Yii::t('app', 'Exp Timestamp'),
            'description' => Yii::t('app', 'Description'),
            'product_id' => Yii::t('app', 'Product ID'),
            'discount_event_id' => Yii::t('app', 'Discount Event ID'),
            'status' => Yii::t('app', 'Status'),
            'limit' => Yii::t('app', 'Limit'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountEvent()
    {
        return $this->hasOne(DiscountEvent::class, ['discount_event_id' => 'discount_event_id']);
    }
}
