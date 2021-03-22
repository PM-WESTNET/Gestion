<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that ususarios menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Usuarios', 'Usuarios', '', true, 'h2');
$I->checkMenuItem('Usuarios', 'Roles', '', true, 'h2');
$I->checkMenuItem('Usuarios', 'Permisos', '', true, 'h2');
$I->checkMenuItem('Usuarios', 'Grupos', '', true, 'h2');
$I->checkMenuItem('Usuarios', 'Log de acceso', '', true, 'li');

$I->checkMenuItem('Vendedores', 'Alta Cliente');