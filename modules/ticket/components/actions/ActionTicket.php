<?php

namespace app\modules\ticket\components\actions;

use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;
use Yii;
use app\modules\ticket\models\Action;


/**
 * This is the model class for table "schema".
 *
 * @property int $schema_id
 * @property string $name
 */
class ActionTicket extends Action implements ActionInterface
{
    /**
     * @param Ticket $ticket
     * Genera un ticket con los valores de configuración que fueron declarados en el estado.
     */
    public static function generate(Ticket $ticket)
    {
        $config = $ticket->status->actionConfig;
        $assignations = $ticket->assignations;

        $new_ticket = new Ticket([
            'status_id' => $config->ticket_status_id,
            'customer_id' => $ticket->customer_id,
            'title' => $config->text_1,
            'content' => $config->text_2,
            'category_id' => $config->ticket_category_id
        ]);
        $new_ticket->save();

        //Si la categoria tiene asignado un responsable, se lo asigno a ese usuario, sino, a los usuarios que estaba asignado el ticket
        $category = Category::findOne($config->ticket_category_id);
        if($category->responsible_user_id) {
            Ticket::assignTicketToUser($new_ticket->ticket_id, $category->responsible_user_id);
        } else {
            foreach ($assignations as $assignation) {
                Ticket::assignTicketToUser($new_ticket->ticket_id, $assignation->user_id);
            }
        }
    }
}
