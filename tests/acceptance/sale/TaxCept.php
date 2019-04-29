<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Tax CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", "Config", "Impuestos");

$I->makeScreenshot('sale_tax_index');

$I->click('Alta Impuesto');

$I->makeScreenshot('sale_tax_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Tax[name]', 'Impuesto A');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_tax_view');

$I->see('Impuesto A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_tax_update');

$I->fillField('Tax[name]', 'Impuesto A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Impuesto A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Impuesto A *');
