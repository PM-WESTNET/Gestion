<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Provider CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Proveedores","Proveedores");

$I->makeScreenshot('provider_provider_index');

$I->click('Alta Proveedor');

$I->makeScreenshot('provider_provider_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Provider[name]', 'Proveedor A');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('provider_provider_view');

$I->see('Proveedor A');
$I->click('Actualizar');

$I->makeScreenshot('provider_provider_update');

$I->fillField('Provider[name]', 'Proveedor A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Proveedor A *');

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Proveedor A *');