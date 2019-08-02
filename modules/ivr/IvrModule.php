<?php

namespace app\modules\ivr;

use app\modules\ivr\v1\models\User;

/**
 * ivr module definition class
 */
class IvrModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\ivr\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        \Yii::$app->set('user',[
                'class' => 'yii\web\User',
                'identityClass' => User::class
            ]);
    }
}
