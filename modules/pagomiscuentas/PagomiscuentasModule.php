<?php

namespace app\modules\pagomiscuentas;

use yii\base\BootstrapInterface;

class PagomiscuentasModule extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'app\modules\pagomiscuentas\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\pagomiscuentas\commands';
        }
    }
}
