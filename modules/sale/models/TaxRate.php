<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "tax_rate".
 *
 * @property integer $tax_rate_id
 * @property string $pct
 * @property integer $tax_id
 * @property integer $code
 *
 * @property ProductHasTaxRate[] $productHasTaxRates
 * @property Product[] $products
 * @property Tax $tax
 */
class TaxRate extends \app\components\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_rate';
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
            [['tax_id', 'pct'], 'required'],
            //Hay impuestos superiores al 100%??
            [['tax_id', 'code'], 'integer'],
            [['pct'], 'number', 'min'=>0, 'max'=>1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tax_rate_id' => Yii::t('app', 'ID'),
            'pct' => Yii::t('app', 'Percentage'),
            'tax_id' => Yii::t('app', 'Tax'),
            'products' => Yii::t('app', 'Products'),
            'tax' => Yii::t('app', 'Tax'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductHasTaxRates()
    {
        return $this->hasMany(ProductHasTaxRate::className(), ['tax_rate_id' => 'tax_rate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->viaTable('product_has_tax_rate', ['tax_rate_id' => 'tax_rate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTax()
    {
        return $this->hasOne(Tax::className(), ['tax_id' => 'tax_id']);
    }
    
    public function getName(){
        return $this->pct * 100 .'%';
    }
    
    /**
     * @inheritdoc
     * Strong relations: Products, Tax.
     */
    public function getDeletable()
    {
        if($this->getProducts()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: ProductHasTaxRates.
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
     * Devuelve el valor de aplicar el porcentaje del impuesto al valor $net
     * @param float $net
     */
    public function calculate($net)
    {
        
        $multiplier = $this->pct ? $this->pct : 0;
        return (float)($net * $multiplier);
        
    }
    
    public static function findRates($tax){
        
        return self::find()->joinWith('tax')->where(['tax.slug'=>$tax])->all();
        
    }

    public static function findTaxCodeByPct($pct)
    {
        $tax_rate = self::find()->where(['round(pct,3)'=>$pct])->one();
        return $tax_rate->code;
    }

    public static function findTaxRateByPct($pct)
    {
        $tax_rate = self::find()->where(['round(pct,3)'=>$pct])->one();
        return $tax_rate;
    }
    
    public function fields() 
    {
        return [
            'pct',
            'code',
            'tax'
        ];
    }
}
