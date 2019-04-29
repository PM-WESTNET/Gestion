<?php

namespace app\modules\ticket;

use yii\base\BootstrapInterface;

class TicketModule extends \yii\base\Module implements BootstrapInterface {

    public $controllerNamespace = 'app\modules\ticket\controllers';
    public $params;

    public function init() {
        parent::init();

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');
        //Traducciones
        $this->registerTranslations();
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\ticket\commands';
        }
    }

    //Registra las traducciones
    public function registerTranslations() {
        \Yii::$app->i18n->translations['modules/ticket/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/ticket/messages',
            'fileMap' => [
                'modules/ticket/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        return \Yii::t('modules/ticket/' . $category, $message, $params, $language);
    }

}
