<?php

namespace app\modules\ivr\v1;
use app\modules\ivr\v1\models\User;

/**
 * v1 module definition class
 */
class V1Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\ivr\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
