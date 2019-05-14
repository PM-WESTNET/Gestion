<?php

namespace app\modules\cobrodigital;

use yii\base\BootstrapInterface;

class CobroDigitalModule extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'app\modules\cobrodigital\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\cobrodigital\commands';
        }
    }
}
