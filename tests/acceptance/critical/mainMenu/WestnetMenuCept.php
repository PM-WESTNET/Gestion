<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that westnet menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Westnet', 'Servidores');
$I->checkMenuItem('Westnet', 'Nodos');
$I->checkMenuItem('Westnet', 'Vendedores');
$I->checkMenuItem('Westnet', 'IPs asignadas');
$I->checkMenuItem('Westnet', 'Ads vacíos no usados');
$I->checkMenuItem('Westnet', 'Crear ADS vacíos');
$I->checkMenuItem('Westnet', 'Ecopagos');
$I->checkMenuItem('Westnet', 'Cobradores');
$I->checkMenuItem('Westnet', 'Recaudadores');
$I->checkMenuItem('Westnet', 'Pagos en Ecopagos');
$I->checkMenuItem('Westnet', 'Cierres de lote');
$I->checkMenuItem('Westnet', 'Cierres diarios');
$I->checkMenuItem('Westnet', 'Notificaciones');
$I->checkMenuItem('Westnet', 'Transportes');
$I->checkMenuItem('Westnet', 'Asignación de Descuento a Clientes');
$I->checkMenuItem('Westnet', 'Asignación de Plan a Clientes');
$I->checkMenuItem('Westnet', 'Asignación de Sucursal a Clientes');
