<?php

namespace app\modules\media\models;

use yii\db\ActiveQuery;

class Type extends ActiveQuery
{
    
    public $type;
    
    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(['type' => $this->type]);
        }
        return parent::prepare($builder);
    }
}