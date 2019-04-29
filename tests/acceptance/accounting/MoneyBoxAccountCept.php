<?php
// En esta prueba considera la existencia de un tipo de entidad llamada banco

$I = new WebGuy($scenario);
$I->wantTo('ensure that Money Box Account CRUD works');

$I->loginAsAdmin();

$I->clickMainMenu("Contabilidad", "Cuentas Monetarias");

$I->makeScreenshot('accounting_moneybox_account_index');

$I->click('Alta Cuenta Monetaria');

$I->makeScreenshot('accounting_moneybox_account_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Entidad Monetaria no puede estar vacío.');
$I->see('Número no puede estar vacío.');

// Verifico comportamiento correcto

$I->selectOption('MoneyBoxAccount[money_box_id]', 'Banco Regional de Cuyo');
$I->fillField('MoneyBoxAccount[number]', '1234');
$I->selectOption('MoneyBoxAccount[currency_id]', 'Peso');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_moneybox_account_view');

$I->see('Banco Regional de Cuyo - 1234', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('accounting_moneybox_account_update');

$I->fillField('MoneyBoxAccount[number]', '1235');
$I->click('Actualizar');
$I->wait(1);

$I->see('Banco Regional de Cuyo - 1235', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('1235');