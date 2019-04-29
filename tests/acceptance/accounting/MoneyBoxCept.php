<?php
// En esta prueba considera la existencia de un tipo de entidad llamada banco

$I = new WebGuy($scenario);
$I->wantTo('ensure that Money Box CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", "Entidades Monetarias");

$I->makeScreenshot('accounting_moneybox_index');

$I->click('Alta Entidad Monetaria');

$I->makeScreenshot('accounting_moneybox_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Tipo de Entidad Monetaria no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

// Verifico comportamiento correcto

$I->fillField('MoneyBox[name]', 'Entidad A');
$I->selectOption('MoneyBox[money_box_type_id]', 'Banco');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_moneybox_view');

$I->see('Entidad A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('accounting_moneybox_update');

$I->fillField('MoneyBox[name]', 'Entidad A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Entidad A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Entidad A *');