<?php

$random =  rand(1111, 9999);
$name = 'Cashier ' . $random;
$username = 'user'.$random;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Cashier CRUD works');

$I->loginAsAdmin();

$I->clickMainMenu("Westnet","Cobradores");

$I->click('Crear Cobrador');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Apellido no puede estar vacío.');
$I->see('Código no puede estar vacío.');
$I->see('Tipo de documento no puede estar vacío.');
$I->see('Número de documento no puede estar vacío.');
$I->see('Nombre de usuario no puede estar vacío.');
$I->see('Contraseña no puede estar vacío.');
$I->see('Repetir contraseña no puede estar vacío.');
$I->see('Ecopago no puede estar vacío.');

// Verifico no duplicación de cobradores y contraseña faltante

$I->fillField('Cashier[name]', $name);
$I->fillField('Cashier[lastname]', $name);
$I->fillField('Cashier[number]', '123'); // Ya usado
$I->selectOption('Cashier[document_type]', 'DNI');
$I->fillField('Cashier[document_number]', $random);
$I->fillField('Cashier[username]', $username);
$I->fillField('Cashier[password]', $username);
$I->fillField('Cashier[password_repeat]', $random);
$I->selectOption('Cashier[ecopago_id]', 'Martínez');

$I->click('Alta');
$I->wait(1);

$I->see('Las contraseñas no coinciden.');

$I->fillField('Cashier[password_repeat]', $username);

$I->click('Alta');
$I->wait(1);

$I->see('Código "123" ya ha sido utilizado.');

// Verifico comportamiento correcto

$I->fillField('Cashier[number]', $random);
$I->click('Alta');
$I->wait(1);

$I->see($name, 'h1');

// Verifico edición

$I->click('Actualizar');

$I->fillField('Cashier[name]', $name . ' edited');
$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' edited', 'h1');

// Pruebo login

$I->click("//span[contains(@class,'user')]/..");
$I->click('Logout');
$I->wait(1);

$I->login($username, $username);

$I->see('Bienvenido a Ecopagos!');

$I->click('Opciones del cobrador');
$I->wait(1);
$I->click('Cambiar cobrador');
$I->wait(1);

$I->loginAsAdmin();

$I->clickMainMenu("Westnet","Cobradores");

$I->click('Cobrador');
$I->wait(1);

$I->click('Cobrador');
$I->wait(1);

$I->click("(//a[@title = 'Ver'])[1]");

// Verifico eliminación

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);

// Verifico que usuario ha sido eliminado

$I->clickMainMenu("Usuarios","Usuarios");

$I->dontSee($username);
