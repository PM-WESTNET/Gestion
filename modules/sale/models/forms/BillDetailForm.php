<?php

namespace app\modules\sale\models\forms;

use Yii;
use yii\base\Model;
use app\modules\sale\models\TaxRate;

/**
 * Description of BillDetailForm
 *
 * @author mmoyano
 */
class BillDetailForm extends Model{
    
    public $concept;
    public $unit_net_price;
    public $tax_rate_id;
    public $qty;
    public $unit_id;

    public function init() {
        parent::init();
        
        $this->qty = 1;
    }
    
    /**
     *
     */
    public function rules() {
        
        return [
            [['concept', 'qty', 'unit_net_price', 'unit_id'], 'required'],
            [['concept'], 'string', 'max' => 255],
            [['qty', 'unit_net_price'], 'double'],
            [['tax_rate_id'], 'exist', 'targetClass' => TaxRate::className()],
        ];
        
    }
    
    public function attributeLabels() 
    {
        return [
            'unit_id' => Yii::t('app', 'Unit'),
            'unitFinalPrice' => Yii::t('app', 'Unit Amount'),
            'unit_net_price' => Yii::t('app', 'Unit Net Price'),
            'concept' => Yii::t('app', 'Concept'),
            'qty' => Yii::t('app', 'Qty'),
            'tax_rate_id' => Yii::t('app', 'Tax rate'),
        ];
    }
    
    public function getUnitFinalPrice()
    {
        return $this->unit_net_price + $this->taxRate->calculate($this->unit_net_price);
    }
    
    public function getTaxRate()
    {
    
        return TaxRate::findOne($this->tax_rate_id);
        
    }
    
}
