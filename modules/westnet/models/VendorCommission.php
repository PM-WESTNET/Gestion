<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "vendor_commission".
 *
 * @property integer $vendor_commission_id
 * @property string $name
 * @property double $percentage
 * @property double $value
 *
 * @property Vendor[] $vendors
 */
class VendorCommission extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vendor_commission';
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
            'vendor_commission_id' => Yii::t('westnet', 'Vendor Commission ID'),
            'name' => Yii::t('app', 'Name'),
            'percentage' => Yii::t('app', 'Percentage'),
            'value' => Yii::t('app', 'Value'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendors()
    {
        return $this->hasMany(Vendor::className(), ['vendor_commission_id' => 'vendor_commission_id']);
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
     * Weak relations: Vendors.
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
        
        return \yii\helpers\ArrayHelper::map($items, 'vendor_commission_id', 'name');
    }

}
