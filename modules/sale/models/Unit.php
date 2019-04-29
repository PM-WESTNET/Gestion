<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "unit".
 *
 * @property integer $unit_id
 * @property string $name
 * @property string $type
 * @property string $symbol
 * @property string $symbol_position
 * @property integer $code
 *
 * @property Product[] $products
 */
class Unit extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'symbol', 'code'], 'required'],
            [['symbol_position'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['symbol'], 'string', 'max' => 10],
            [['symbol_position'], 'in', 'range'=>['prefix','suffix']],
            [['type'], 'in', 'range'=>['int','float']],
            [['code'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'unit_id' => Yii::t('app', 'Unit ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'symbol' => Yii::t('app', 'Symbol'),
            'symbol_position' => Yii::t('app', 'Symbol Position'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['unit_id' => 'unit_id']);
    }
    
    public function getDeletable(){
    
        if($this->getProducts()->exists()){
            return false;
        }
        return true;
        
    }
}
