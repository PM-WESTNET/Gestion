<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 18/03/19
 * Time: 12:26
 */

$status = [
    ['status_id' => '1','name' => 'nuevo','description' => NULL,'is_open' => '1','generate_action' => '0'],
    ['status_id' => '2','name' => 'en curso (asignado]','description' => NULL,'is_open' => '1','generate_action' => '0'],
    ['status_id' => '3','name' => 'en curso (planificado]','description' => NULL,'is_open' => '1','generate_action' => '0'],
    ['status_id' => '4','name' => 'en espera','description' => NULL,'is_open' => '1','generate_action' => '0'],
    ['status_id' => '5','name' => 'cerrado (resuelto]','description' => NULL,'is_open' => '0','generate_action' => '0'],
    ['status_id' => '6','name' => 'cerrado (no resuelto]','description' => NULL,'is_open' => '0','generate_action' => '0'],
    ['status_id' => '7','name' => 'Compromiso de pago','description' => 'Se compromete a realizar el pago','is_open' => '1','generate_action' => '1'],
    ['status_id' => '8','name' => 'Extensión','description' => 'Extensión de pago','is_open' => '1','generate_action' => '0'],
    ['status_id' => '9','name' => 'Informado','description' => 'Ha sido informado','is_open' => '1','generate_action' => '0'],
    ['status_id' => '10','name' => 'No va a pagar','description' => 'Informa que no va a pagar','is_open' => '0','generate_action' => '0'],
    ['status_id' => '11','name' => 'Plan de pago','description' => 'Se realiza plan de pago','is_open' => '0','generate_action' => '0'],
    ['status_id' => '12','name' => 'Problemas técnicos','description' => 'Presenta problemas técnicos','is_open' => '1','generate_action' => '0'],
    ['status_id' => '13','name' => 'Pago parcial','description' => 'Se realiza un pago parcial','is_open' => '0','generate_action' => '0'],
    ['status_id' => '14','name' => 'Tel erróneo sin comunicación','description' => 'No es posible la comunicación','is_open' => '0','generate_action' => '0'],
    ['status_id' => '15','name' => 'Pagó','description' => 'Informa un pago realizado','is_open' => '0','generate_action' => '0'],
    ['status_id' => '16','name' => 'Pago 1 de 2','description' => 'Informa primer cuota pagada de dos cuotas totales','is_open' => '0','generate_action' => '0'],
    ['status_id' => '43','name' => 'Baja','description' => 'Se requiere la baja del servicio','is_open' => '1','generate_action' => '1']
];

return $status;
