<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Change Cashier  works');

 $I->login('diego', 'diego');

$I->see('Bienvenido a Ecopagos!');

$I->click('Opciones del cobrador');
$I->click('Cambiar cobrador');

$I->see('Autorización');

$I->login('diego', 'diego');

$I->see('Bienvenido a Ecopagos!');
