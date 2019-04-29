<?php

namespace app\modules\accounting;

use yii\base\BootstrapInterface;

class AccountingModule extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'app\modules\accounting\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\accounting\commands';
        }
    }
}