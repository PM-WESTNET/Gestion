<?php

namespace app\components\widgets\agenda\ticket;

use yii\web\AssetBundle;

class TicketBundle extends AssetBundle {

    public $sourcePath = '@app/components/widgets/agenda/ticket/assets';
    public $css = [
    ];
    public $js = [
        'Ticket.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
