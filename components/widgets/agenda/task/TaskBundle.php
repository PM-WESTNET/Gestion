<?php

namespace app\components\widgets\agenda\task;

use yii\web\AssetBundle;

class TaskBundle extends AssetBundle {

    public $sourcePath = '@app/components/widgets/agenda/task/assets';
    public $css = [
        'task.css',
    ];
    public $js = [
        'Task.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
