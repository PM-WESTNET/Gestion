<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\sale\models\Product;

/**
 * This is the model class for table "product_commission".
 *
 * @property integer $product_commission_id
 * @property string $name
 * @property double $percentage
 * @property double $value
 *
 * @property Product[] $products
 */
class ProductCommission extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_commission';
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
            [['name'], 'required'],
            [['percentage'], 'number', 'min' => 0],
            [['value'], 'number'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_commission_id' => Yii::t('westnet', 'Product Commission ID'),
            'name' => Yii::t('app', 'Name'),
            'percentage' => Yii::t('app', 'Percentage'),
            'value' => Yii::t('app', 'Value'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['product_commission_id' => 'product_commission_id']);
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
     * Weak relations: Products.
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
     * Calcula la comision correspondiente para el valor $amount
     * @param double $amount
     * @return double
     */
    public function calculateCommission($amount)
    {
        if($this->percentage > 0){
            return (double)($amount*($this->percentage/100));
        }else{
            return (double)$this->value;
        }
    }
    
    public static function findForSelect()
    {
        $items = self::find()->all();
        
        return \yii\helpers\ArrayHelper::map($items, 'product_commission_id', 'name');
    }

}
