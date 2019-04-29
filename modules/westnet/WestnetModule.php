<?php

namespace app\modules\westnet;

use yii\base\BootstrapInterface;

class WestnetModule extends \yii\base\Module implements BootstrapInterface {

    public $controllerNamespace = 'app\modules\westnet\controllers';

    public function init() {
        parent::init();
        
        $this->modules = [
            'ecopagos' => [
                'class' => 'app\modules\westnet\ecopagos\EcopagosModule',
            ],
            'notifications' => [
                'class' => 'app\modules\westnet\notifications\NotificationsModule',
            ],
            'sequre' => [
                'class' => 'app\modules\westnet\sequre\SequreModule',
            ],
            'api' => [
                'class' => 'app\modules\westnet\api\WestnetAPIModule',
            ],
            'reports' => [
                'class' => 'app\modules\westnet\reports\ReportsModule',
            ]
        ];
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\westnet\commands';
        }
    }
}
