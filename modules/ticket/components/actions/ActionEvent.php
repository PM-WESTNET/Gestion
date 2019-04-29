<?php

namespace app\modules\ticket\components\actions;

use app\modules\agenda\models\Task;
use app\modules\ticket\models\Action;
use app\modules\ticket\models\Ticket;
use Yii;


/**
 * This is the model class for table "schema".
 *
 * @property int $schema_id
 * @property string $name
 */
class ActionEvent extends Action implements ActionInterface
{
    /**
     * @param Ticket $ticket
     * Genera una tarea nueva con los valores de configuración que fueron declarados en el estado.
     */
    public static function generate(Ticket $ticket)
    {
        $config = $ticket->status->actionConfig;
        //Genero un array de asignaciones, para que se puedan generar las notificaciones de la tarea nueva
        $assignations = $ticket->assignations;
        $users = [];
        foreach($assignations as $asignation) {
            $users [] = [$asignation->user_id];
        }

        if(!YII_ENV_TEST){
            $user_id = Yii::$app->user->getId();
        } else {
            $user_id = 1;
        }

        $new_task = new Task([
            'task_type_id' => $config->task_type_id,
            'status_id' => $config->task_status_id,
            'name' => $config->text_1,
            'priority' => $config->task_priority,
            'date' => $ticket->task_date ? ( new \DateTime($ticket->task_date))->format('d-m-Y') : (new \DateTime('now'))->format('d-m-Y'),
            'time' => $config->task_time,
            'duration' => $config->taskCategory->default_duration,
            'users' => $users,
            'creator_id' => $user_id
        ]);
        $new_task->save();
    }
}
