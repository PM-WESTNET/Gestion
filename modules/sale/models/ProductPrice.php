<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "product_price".
 *
 * @property integer $product_price_id
 * @property double $net_price
 * @property double $taxes
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property integer $exp_timestamp
 * @property string $exp_date
 * @property string $exp_time
 * @property integer $update_timestamp
 * @property string $status
 * @property integer $product_id
 * @property double $future_final_price
 *
 * @property Product $product
 */
class ProductPrice extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_price';
    }
    
    public function behaviors()
    {
        return [
            'datestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['net_price', 'taxes', 'future_final_price'], 'number'],
            [['exp_date', 'exp_time'], 'safe'],
            [['date'], 'date'],
            [['time', 'time', 'future_final_price'], 'safe'],
            [['timestamp', 'exp_timestamp', 'update_timestamp', 'product_id'], 'integer'],
            [['status'], 'in', 'range'=> ['updated','outdated']],
            [['product_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_price_id' => Yii::t('app', 'Product Price ID'),
            'net_price' => Yii::t('app', 'Net Price'),
            'taxes' => Yii::t('app', 'Taxes'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'exp_timestamp' => Yii::t('app', 'Exp Timestamp'),
            'exp_date' => Yii::t('app', 'Exp Date'),
            'exp_time' => Yii::t('app', 'Exp Time'),
            'update_timestamp' => Yii::t('app', 'Update Timestamp'),
            'status' => Yii::t('app', 'Status'),
            'product_id' => Yii::t('app', 'Product ID'),
            'future_final_price' => Yii::t('app', 'Future final price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
    
    public function getFinalPrice(){
        
        return $this->net_price + $this->taxes;
        
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            
            if(empty($this->exp_date)){
                $this->exp_timestamp = -1;
            }else{
                $this->exp_date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
                $this->exp_timestamp = strtotime($this->exp_date);
            }
            
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Devuelve el color asociado al producto en formato RGB
     * @return string
     */
    public function getRgb(){
        
        return $this->product->rgb;
        
    }
    
    public function getDeletable(){
    
        return true;
        
    }

    /**
     * @param $new_net_price
     * @param $old_product_price
     * @return bool
     * Determina si el neto, fecha de expiracion o precio a futuro ha cambiado.
     */
    public function arePlanValuesChanged($new_net_price, $old_product_price)
    {
        if(($this->net_price + $new_net_price) != ($old_product_price->net_price + $old_product_price->taxes)) {
            return true;
        }

        if($this->exp_date != $old_product_price->exp_date){
            return true;
        }

        if($this->future_final_price != $old_product_price->future_final_price) {
            return true;
        }

        return false;
    }
    
}
