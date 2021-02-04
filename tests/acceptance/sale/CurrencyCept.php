<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Currency CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", 'Config', "Monedas");

$I->makeScreenshot('sale_currency_index');

$I->click('Alta Moneda');

$I->makeScreenshot('sale_currency_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Código no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Currency[name]', 'Moneda A');
$I->fillField('Currency[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_currency_view');

$I->see('Moneda A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_currency_update');

$I->fillField('Currency[name]', 'Moneda A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Moneda A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Moneda A *');
