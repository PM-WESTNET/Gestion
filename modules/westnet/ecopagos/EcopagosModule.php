<?php

namespace app\modules\westnet\ecopagos;

use Yii;

class EcopagosModule extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\westnet\ecopagos\controllers';

    public function init() {
        parent::init();
        $this->modules = [
            'frontend' => [
                'class' => 'app\modules\westnet\ecopagos\frontend\FrontendModule',
            ],
        ];

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');
        //Traducciones
        $this->registerTranslations();
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\westnet\ecopagos\commands';
        }
    }

    //Registra las traducciones
    public static function registerTranslations() {
        
        \Yii::$app->i18n->translations['modules/westnet/ecopagos/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/westnet/ecopagos/messages',
            'fileMap' => [
                'modules/westnet/ecopagos/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        if ( !isset(Yii::$app->i18n->translations['modules/westnet/ecopagos/*']) )
        {
            EcopagosModule::registerTranslations();
        }
        return Yii::t('modules/westnet/ecopagos/' . $category, $message, $params, $language);
    }

}
