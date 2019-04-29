<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$random = rand(111, 999);
$name = "Vendor $random";
$user = "vendor$random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Vendor CRUD works');

 $I->loginAsAdmin();

$I->clickMainMenu("Westnet", 'Vendedores');

$I->click('Alta Vendedor');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);

$I->see('Usuario no puede estar vacío.');
$I->see('Password no puede estar vacío.');
$I->see('Repeat Password no puede estar vacío.');

// Verifico mensaje de campos requeridos luego de post

$I->fillField('UserVendor[username]', $user);
$I->fillField('UserVendor[password]', $random);
$I->fillField('UserVendor[repeat_password]', $random);

$I->click('Alta');
$I->wait(1);

$I->fillField('UserVendor[name]', $name);
$I->fillField('UserVendor[lastname]', $name);

if (Yii::$app->getModule('zone')) {
    $I->selectOptionForSelect2('Address[zone_id]', 'Centro');
}
if (Yii::$app->getModule('accounting')) {
    $I->selectOptionForSelect2('UserVendor[account]', 'Caja Pesos');
}

$I->click('Alta');
$I->wait(1);

// Verifico comportamiento correcto

$I->fillField('UserVendor[name]', $name);
$I->fillField('UserVendor[lastname]', $name);
$I->selectOption('UserVendor[documentType]', 'DNI');
$I->fillField('UserVendor[documentNumber]', '12345678');
$I->fillField('UserVendor[email]', 'cliente@fake.com');
$I->fillField('UserVendor[phone]', '1234567890');
if (Yii::$app->getModule('accounting')) {
    $I->selectOptionForSelect2('UserVendor[account]', 'Caja Pesos');
}
if (Yii::$app->getModule('zone')) {
    $I->selectOptionForSelect2('Address[zone_id]', 'Centro');
}
$I->fillField('Address[street]', 'San Martín');
$I->fillField('Address[number]', '1329');
$I->fillField('Address[between_street_1]', 'Buenos Aires');
$I->fillField('Address[between_street_2]', 'Lavalle');
$I->fillField('Address[block]', 'J1');
$I->fillField('Address[house]', '13');
$I->fillField('Address[tower]', 'K2');
$I->fillField('Address[floor]', '32');
$I->fillField('Address[department]', 'O3');
$I->fillField('Address[geocode]', '-32.88821, -68.83807');
$I->click('Alta');
$I->wait(1);

$I->see($name, 'h1');

$I->click('Actualizar');
$I->waitForText('Actualizar Vendedor');

$I->fillField('UserVendor[name]', $name . ' EDIT');
$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' EDIT', 'h1');

$I->click("Eliminar");
$I->acceptPopup();
$I->wait(1);

$I->dontSee($name);

$I->clickMainMenu("Usuarios", 'Usuarios');

$I->dontSee($user);
