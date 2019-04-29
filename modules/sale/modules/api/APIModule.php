<?php

namespace app\modules\sale\modules\api;

class APIModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\sale\modules\api\controllers';

    public function init()
    {
        parent::init();
        
        \Yii::$app->user->enableSession = false;
    }
}
