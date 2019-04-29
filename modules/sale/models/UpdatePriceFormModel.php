<?php

namespace app\modules\sale\models;

use Yii;

class UpdatePriceFormModel extends \yii\base\Model {
    
    public $percentage;
    
    public $exp_date;
    
    public $expired = false;
    
    public $filter;
    
    public $items;
    
    public $category;
    
    public function rules(){
        
        return [
            [['percentage'],'required'],
            [['percentage'],'double','min'=>-100,'max'=>1000],
            [['expired'],'boolean'],
            [['exp_date'],'date'],
            [['items','category'],'safe'],
            [['filter'],'in','range'=>['all','selected','expired','active']],
        ];
        
    }
   
    public function attributeLabels() {
        return [
            'percentage'=>Yii::t('app','Percentage'),
            'exp_date'=>Yii::t('app','Expiration date'),
            'filter'=>Yii::t('app','Filter'),
            'category'=>Yii::t('app','Category'),
        ];
    }
    
}
