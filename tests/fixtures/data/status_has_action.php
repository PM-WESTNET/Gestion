<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:50
 */

$status_has_action = [
    [
        'status_has_action_id' => 3,
        'status_id' => 7,
        'action_id' => 4,
        'text_1' => 'Cliente tiene un compromiso de pago',
        'text_2' => NULL,
        'ticket_category_id' => NULL,
        'task_category_id' => 1,
        'task_type_id' => 2,
        'ticket_status_id' => NULL,
        'task_status_id' => 1,
        'task_priority' => 2,
        'task_time' => 13
    ],
    [
        'status_has_action_id' => 4,
        'status_id' => 43,
        'action_id' => 3,
        'text_1' => 'Baja requerida',
        'text_2' => 'Se requiere la baja del servicio',
        'ticket_category_id' => 4,
        'task_category_id' => NULL,
        'task_type_id' => NULL,
        'ticket_status_id' => 1,
        'task_status_id' => NULL,
        'task_priority' => NULL,
        'task_time' => NULL
    ]
];


return $status_has_action;