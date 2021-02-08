<?php

namespace app\modules\westnet\notifications;

class NotificationsModule extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\westnet\notifications\controllers';

    public function init() {
        parent::init();

        $this->modules = [
            'integratech' => [
                'class' => 'app\modules\westnet\notifications\modules\integratech\IntegratechModule',
                'modules' => [
                    'v1' => [
                        'class' => 'app\modules\westnet\notifications\modules\integratech\v1\V1Module',
                    ],
                ],
            ],
            'infobip' => [
                'class' => 'app\modules\westnet\notifications\modules\infobip\InfobipModule',
            ],
            ];

        //Module parameters
        $this->params = require(__DIR__ . '/params.php');
        //Traducciones
        $this->registerTranslations();
    }


    //Registra las traducciones
    public static function registerTranslations() {

        \Yii::$app->i18n->translations['modules/westnet/notifications/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/westnet/notifications/messages',
            'fileMap' => [
                'modules/westnet/notifications/app' => 'app.php',
            ],
        ];
    }

    //Habilita las traducciones
    public static function t($category, $message, $params = [], $language = null) {
        if ( !isset(\Yii::$app->i18n->translations['modules/westnet/notifications/*']) )
        {
            NotificationsModule::registerTranslations();
        }
        return \Yii::t('modules/westnet/notifications/' . $category, $message, $params, $language);
    }

}
