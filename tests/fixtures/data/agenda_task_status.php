<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 11:04
 */

$status = [
    ['status_id' => '1','name' => 'Nueva','description' => 'Tarea nueva','color' => 'normal','slug' => 'created'],
    ['status_id' => '2','name' => 'Pendiente','description' => 'Tarea pendiente','color' => 'warning','slug' => 'pending'],
    ['status_id' => '3','name' => 'En progreso','description' => 'Tarea en progreso','color' => 'info','slug' => 'in_progress'],
    ['status_id' => '4','name' => 'Detenida','description' => 'Tarea detenida','color' => 'danger','slug' => 'stopped'],
    ['status_id' => '5','name' => 'Completada','description' => 'Tarea completada','color' => 'success','slug' => 'completed']
];
return $status;