<?php

namespace app\modules\config;

use Yii;

class ConfigModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\config\controllers';

    public function init()
    {
        parent::init();
        
        $this->registerTranslations();
    }
    
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/config/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/config/messages',
            'fileMap' => [
                'modules/config/config' => 'config.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/config/' . $category, $message, $params, $language);
    }

}
