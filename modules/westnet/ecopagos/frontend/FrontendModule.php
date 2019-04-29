<?php

namespace app\modules\westnet\ecopagos\frontend;

class FrontendModule extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\westnet\ecopagos\frontend\controllers';

    public function init() {
        parent::init();

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');

        //Traducciones
        $this->registerTranslations();
        
        //Set alias
        $this->setAliases([
            '@assets' => '../modules/westnet/ecopagos/frontend/assets/'
        ]);
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\westnet\ecopagos\frontend\commands';
        }
    }

    //Registra las traducciones
    public function registerTranslations() {
        \Yii::$app->i18n->translations['modules/westnet/ecopagos/frontend/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/westnet/ecopagos/frontend/messages',
            'fileMap' => [
                'modules/westnet/ecopagos/frontend/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        return \Yii::t('modules/westnet/ecopagos/frontend/' . $category, $message, $params, $language);
    }

}
