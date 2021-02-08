<?php

namespace app\modules\media;

Use Yii;

class MediaModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\media\controllers';

    public function init()
    {
        parent::init();

        Yii::setAlias('@media', '@app/modules/media');
        
        Yii::configure($this, require(__DIR__ . '/config/media.php'));
    }
}
