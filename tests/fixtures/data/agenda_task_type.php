<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 11:04
 */

$task_type = [
    ['task_type_id' => 1,'name' => 'Tarea global','description' => 'Esta tarea sera notificada a todos los usuarios del sistema','slug' => 'global'],
    ['task_type_id' => 2,'name' => 'Tarea por usuario','description' => 'Esta tarea sera notificada a usuarios seleccionados','slug' => 'by_user']
];

return $task_type;