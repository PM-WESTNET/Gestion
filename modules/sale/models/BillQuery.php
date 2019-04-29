<?php

namespace app\modules\sale\models;



/**
 * Description of BillQuery
 *
 * @author mmoyano
 */
class BillQuery extends \yii\db\ActiveQuery {
    
    public $class;
    
    public function prepare($builder)
    {
        $this->andWhere(['class' => $this->class]);
        return parent::prepare($builder);
    }
    
}
