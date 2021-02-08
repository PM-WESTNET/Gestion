<?php

namespace app\modules\ticket\components\actions;

use app\modules\ticket\models\Ticket;

/**
 *
 * @author mmoyano
 */
interface ActionInterface {

    public static function generate(Ticket $ticket);
}
