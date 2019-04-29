<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Unit CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", "Config", "Unidades");

$I->makeScreenshot('sale_unit_index');

$I->click('Alta Unidad');

$I->makeScreenshot('sale_unit_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Símbolo no puede estar vacío.');
$I->see('Código no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('Unit[code]', 'BAD');
$I->click('Alta');
$I->wait(1);
$I->see('Código debe ser un número entero.');

// Verifico comportamiento correcto

$I->fillField('Unit[name]', 'Unidad A');
$I->fillField('Unit[symbol]', 'A');
$I->fillField('Unit[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_unit_view');

$I->see('Unidad A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_unit_update');

$I->fillField('Unit[name]', 'Unidad A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Unidad A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Unidad A *');
