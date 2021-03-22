<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Change Cashier Password works');

 $I->login('diego', 'diego');

$I->see('Bienvenido a Ecopagos!');

$I->click('Opciones del cobrador');
$I->click('Cambiar contraseña');

// Verifico campos requeridos

$I->click('Actualizar');
$I->wait(1);

$I->see('Contraseña no puede estar vacío.');
$I->see('Repetir contraseña no puede estar vacío.');

$random = rand(111111, 999999);

$I->fillField('Cashier[password]', $random);
$I->fillField('Cashier[password_repeat]', 'abc');

$I->click('Actualizar');
$I->wait(1);

$I->see('Las contraseñas no coinciden.');

// Verifico comportamiento correcto

$I->fillField('Cashier[password_repeat]', $random);

$I->click('Actualizar');
$I->wait(1);

$I->see('Contraseña actualizada con éxito!');

// Verifico que nueva credencial

$I->click('Opciones del cobrador');
$I->click('Cambiar cobrador');

$I->login('diego', $random);

$I->see('Bienvenido a Ecopagos!');

// Vuelvo a contraseña inicial

$I->click('Opciones del cobrador');
$I->click('Cambiar contraseña');

$I->fillField('Cashier[password]', 'diego');
$I->fillField('Cashier[password_repeat]', 'diego');

$I->click('Actualizar');
$I->wait(1);

$I->see('Contraseña actualizada con éxito!');
