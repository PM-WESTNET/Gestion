<?php

namespace app\modules\westnet\api;

class WestnetAPIModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\westnet\api\controllers';

    public function init()
    {
        parent::init();
        
        \Yii::$app->user->enableSession = false;
    }
}
