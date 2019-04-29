<?php

namespace app\modules\agenda;

use yii\base\BootstrapInterface;

class AgendaModule extends \yii\base\Module implements BootstrapInterface {

    public $controllerNamespace = 'app\modules\agenda\controllers';

    public function init() {
        parent::init();

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');
        //Traducciones
        $this->registerTranslations();
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\agenda\commands';
        }
    }

    //Registra las traducciones
    public function registerTranslations() {
        \Yii::$app->i18n->translations['modules/agenda/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/agenda/messages',
            'fileMap' => [
                'modules/agenda/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        return \Yii::t( $category, $message, $params, $language);
    }

}
