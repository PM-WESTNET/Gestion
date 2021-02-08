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
class ProductPriceForm extends Model{
    
    public $net;
    public $final;

    /**
     *
     */
    public function rules() {
        
        return [
            ['net', 'required', 'when' => function($model){ return empty($model->final); }],
            ['final', 'required', 'when' => function($model){ return empty($model->net); }],
            [['net', 'final'], 'double', 'min' => 0],
        ];
        
    }
    
    public function attributeLabels() 
    {
        return [
            'final' => Yii::t('app', 'Final Price'),
            'net' => Yii::t('app', 'Net Price'),
        ];
    }
    
}
