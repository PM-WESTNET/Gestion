<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that productos menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Productos', 'Productos');
$I->checkMenuItem('Productos', 'Categorías');
$I->checkMenuItem('Productos', 'Planes');
$I->checkMenuItem('Productos', 'Características de Plan');
$I->checkMenuItem('Productos', 'Importación de Productos', '', false);
$I->checkMenuItem('Productos', 'Movimientos de Stock');
