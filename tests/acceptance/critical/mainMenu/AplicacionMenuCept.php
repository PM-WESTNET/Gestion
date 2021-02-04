<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that aplicacion menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Aplicación', 'Logs', '', false);
if (Yii::$app->params['companies']['enabled']) {
    $I->checkMenuItem('Aplicación', 'Empresas', '', false);
    $I->checkMenuItem('Aplicación', 'Puntos de Venta', '', false);
}
$I->checkMenuItem('Aplicación', 'Contabilidad', '', false);
$I->checkMenuItem('Aplicación', 'Comprobantes', '', false);
$I->checkMenuItem('Aplicación', 'Gestión de Stock', '', false);
$I->checkMenuItem('Aplicación', 'Agenda', '', false);
$I->checkMenuItem('Aplicación', 'Media', '', false);
$I->checkMenuItem('Aplicación', 'Productos', '', false);
$I->checkMenuItem('Aplicación', 'Customer', '', false);
$I->checkMenuItem('Aplicación', 'Sequre', '', false);
$I->checkMenuItem('Aplicación', 'Westnet', '', false);
$I->checkMenuItem('Aplicación', 'Ecopago', '', false);
$I->checkMenuItem('Aplicación', 'Ticket', '', false);
$I->checkMenuItem('Aplicación', 'Categorías de configuración', '', false);
$I->checkMenuItem('Aplicación', 'Ítems de configuración', '', false);
