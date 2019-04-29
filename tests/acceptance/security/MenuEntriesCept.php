<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that login works');

$I = LoginPage::openBy($I);

$I->amGoingTo('try to login with empty credentials');
$I->login('roleless_user', 'roleless_user');

$I->see('No tiene permitido ejecutar esta acción.');

$I->dontSee('Facturación');
$I->dontSee('Comprobantes');
$I->dontSee('Clientes');
$I->dontSee('Proveedores');
$I->dontSee('Productos');
$I->dontSee('Contabilidad');
$I->dontSee('Agenda');
$I->dontSee('Tickets');
$I->dontSee('Usuarios');
$I->dontSee('Aplicación');