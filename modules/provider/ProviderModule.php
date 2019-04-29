<?php

namespace app\modules\provider;

class ProviderModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\provider\controllers';

    public function init()
    {
        parent::init();
        if(\Yii::$app instanceof \yii\console\Application){
            $this->controllerNamespace = 'app\modules\provider\commands';
        }
        // custom initialization code goes here
    }
}
