<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Money Box Type CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", "Tipos de Entidad Monetaria");

$I->makeScreenshot('accounting_moneybox_type_index');

$I->click('Alta Tipo de Entidad Monetaria');

$I->makeScreenshot('accounting_moneybox_type_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('MoneyBoxType[name]', 'Tipo Entidad A');
$I->fillField('MoneyBoxType[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_moneybox_type_view');

$I->see('Tipo Entidad A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('accounting_moneybox_type_update');

$I->fillField('MoneyBoxType[name]', 'Tipo Entidad A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Tipo Entidad A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Tipo Entidad A *');