<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "funding_plan".
 *
 * @property integer $funding_plan_id
 * @property integer $product_id
 * @property integer $qty_payments
 * @property double $amount_payment
 * @property string $status
 *
 * @property Product $product
 */
class FundingPlan extends \app\components\db\ActiveRecord
{

    private $_products;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'funding_plan';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'qty_payments', 'amount_payment'], 'required'],
            [['product_id', 'qty_payments'], 'integer'],
            [['amount_payment'], 'number'],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['product'], 'safe'],
            ['qty_payments', 'compare', 'compareValue' => 1, 'operator' => '>'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'funding_plan_id' => Yii::t('app', 'Funding Plan ID'),
            'product_id' => Yii::t('app', 'Product'),
            'qty_payments' => Yii::t('app', 'Quantity Payments'),
            'amount_payment' => Yii::t('app', 'Amount Payment'),
            'status' => Yii::t('app', 'Status'),
            'product' => Yii::t('app', 'Product'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

                 
    /**
     * @inheritdoc
     * Strong relations: ProductHasFundingPlans, Products.
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

    public function getFullName()
    {
        return $this->qty_payments . " " . Yii::t('app', 'payments of') . " " .  Yii::$app->getFormatter()->asCurrency($this->getFinalAmount());
    }

    public function getFinalTaxesAmount()
    {
        return $this->product->calculateTaxes($this->amount_payment);
    }

    public function getFinalAmount()
    {
        return $this->amount_payment + $this->product->calculateTaxes($this->amount_payment);
    }

    public function getFinalTotalAmount()
    {
        return ($this->amount_payment * $this->qty_payments ) + $this->product->calculateTaxes($this->amount_payment * $this->qty_payments);
    }
}
