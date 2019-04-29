<?php

$key = rand(100, 999);
$edit = '#';
$name = "Cuenta $key";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Account CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", "Plan de Cuentas");
$I->makeScreenshot('accounting_account_index');

$I->click('Alta Cuenta');
$I->wait(1);

$I->makeScreenshot('accounting_account_create');

// Verifico mensaje de campos requeridos

$I->switchToIFrame("account-iframe");
$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Account[name]', $name);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_account_view');

$I->see($name);
$I->click('Actualizar');
$I->wait(1);

$I->makeScreenshot('accounting_account_update');

$I->fillField('Account[name]', $name . $edit);
$I->click('Actualizar');
$I->wait(1);

$I->switchToIFrame();
$I->fillField('search_tree', $name . $edit);
$I->wait(1);
$I->switchToIFrame("account-iframe");
$I->see($name . $edit);

$I->click('Eliminar');
$I->acceptPopup();
$I->wait(1);

$I->switchToIFrame();
$I->dontSee($name . $edit);
