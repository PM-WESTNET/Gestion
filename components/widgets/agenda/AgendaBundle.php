<?php

namespace app\components\widgets\agenda;

use yii\web\AssetBundle;

class AgendaBundle extends AssetBundle {

    public $sourcePath = '@app/components/widgets/agenda/assets';
    public $css = [
        'agenda.css',
    ];
    public $js = [
        'Agenda.js',
        'AgendaTask.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
