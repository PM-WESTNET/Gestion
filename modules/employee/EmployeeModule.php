<?php

namespace app\modules\employee;

class EmployeeModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\employee\controllers';

    public function init()
    {
        parent::init();
        if(\Yii::$app instanceof \yii\console\Application){
            $this->controllerNamespace = 'app\modules\employee\commands';
        }
        // custom initialization code goes here
    }
}
