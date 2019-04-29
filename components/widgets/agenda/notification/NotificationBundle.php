<?php

namespace app\components\widgets\agenda\notification;

use yii\web\AssetBundle;

class NotificationBundle extends AssetBundle {

    public $sourcePath = '@app/components/widgets/agenda/notification/assets';
    public $css = [
        'notification.css',
    ];
    public $js = [
        'Notification.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
