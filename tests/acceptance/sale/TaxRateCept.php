<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Tax Rate CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", "Config", "Tasas impositivas");

$I->makeScreenshot('sale_tax_rate_index');

$I->click('Alta Tasa impositiva');

$I->makeScreenshot('sale_tax_rate_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Porcentaje no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('TaxRate[pct]', 'BAD');
$I->click('Alta');
$I->wait(1);
$I->see('Porcentaje debe ser un número.');

// Verifico comportamiento correcto

$I->fillField('TaxRate[pct]', '0.063');
$I->fillField('TaxRate[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_tax_rate_view');

$I->see('0.063');
$I->click('Actualizar');

$I->makeScreenshot('sale_tax_rate_update');

$I->fillField('TaxRate[pct]', '0.064');
$I->click('Actualizar');
$I->wait(1);

$I->see('0.064');

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Moneda A *');
