<?php

namespace app\modules\westnet\sequre;

use yii\base\BootstrapInterface;

class SequreModule extends \yii\base\Module implements BootstrapInterface {

    public $controllerNamespace = 'app\modules\westnet\sequre\controllers';

    public function init() {
        parent::init();

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');
        //Traducciones
        $this->registerTranslations();
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\westnet\sequre\commands';
        }
    }

    //Registra las traducciones
    public function registerTranslations() {
        \Yii::$app->i18n->translations['modules/westnet/sequre/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/westnet/sequre/messages',
            'fileMap' => [
                'modules/westnet/sequre/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        return \Yii::t('modules/westnet/sequre/' . $category, $message, $params, $language);
    }

}
