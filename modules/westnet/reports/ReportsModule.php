<?php

namespace app\modules\westnet\reports;

use Yii;
use yii\base\BootstrapInterface;

class ReportsModule extends \yii\base\Module implements BootstrapInterface {

    public $controllerNamespace = 'app\modules\westnet\reports\controllers';

    public function init() {
        parent::init();

        //Traducciones
        $this->registerTranslations();
    }

    //Registra las traducciones
    public static function registerTranslations() {
        
        \Yii::$app->i18n->translations['modules/westnet/reports/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/westnet/reports/messages',
            'fileMap' => [
                'modules/westnet/reports/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        if ( !isset(Yii::$app->i18n->translations['modules/westnet/reports/*']) )
        {
            ReportsModule::registerTranslations();
        }
        return Yii::t('modules/westnet/reports/' . $category, $message, $params, $language);
    }

    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\westnet\reports\commands';
        }
    }

}
