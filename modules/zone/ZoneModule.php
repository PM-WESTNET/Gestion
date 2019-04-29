<?php

namespace app\modules\zone;

use yii\base\BootstrapInterface;

class ZoneModule extends \yii\base\Module implements BootstrapInterface 
{
    public $controllerNamespace = 'app\modules\zone\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\zone\commands';
        }
    }
}
