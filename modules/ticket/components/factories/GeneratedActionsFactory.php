<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/06/15
 * Time: 14:21
 */

namespace app\modules\ticket\components\factories;

use app\modules\ticket\components\actions\ActionEvent;
use app\modules\ticket\components\actions\ActionTicket;
use app\modules\ticket\models\Action;
use app\modules\ticket\models\Ticket;

class GeneratedActionsFactory {

    public function generate(Ticket $ticket){
        $action = $ticket->status->action;

        //Si el estado tiene asociada una acción
        if($action) {
            if($action->type == Action::TYPE_EVENT) {
                ActionEvent::generate($ticket);
            }

            if($action->type == Action::TYPE_TICKET) {
                ActionTicket::generate($ticket);
            }
        }
    }
}