<?php

namespace app\modules\mobileapp;

/**
 * mobileapp module definition class
 */
class MobileAppApiModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\mobileapp\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->params['apiRequest'] = true;
        // custom initialization code goes here
    }
}
