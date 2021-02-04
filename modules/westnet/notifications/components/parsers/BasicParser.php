<?php

namespace app\modules\westnet\notifications\components\parsers;

use Yii;
use yii\base\Component;

/**
 * Description of BasicParser
 *
 * @author mmoyano
 */
class BasicParser extends Component {
    
    public function attributeLabels()
    {
        //En caso de tener mas de un plan, solo se toma el primero
        return [
            'customer.fullname' => Yii::t('app', 'Customer Fullname'),
            'contract.plan' => Yii::t('app', 'Plan'),
            'contract.plan.price' => Yii::t('app', 'Plan price'),
            'contract.plan.previous_price' => Yii::t('app', 'Previous plan price'),
            'contract.balance' => Yii::t('app', 'Balance'),
        ];
    }
    
}
