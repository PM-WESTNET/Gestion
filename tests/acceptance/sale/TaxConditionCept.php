<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Tax Condition CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", 'Config', "Condiciones frente a IVA");

$I->makeScreenshot('sale_tax_condition_index');

$I->click('Alta Condición frente a IVA');

$I->makeScreenshot('sale_tax_condition_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Tipo de documento requerido no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('TaxCondition[name]', 'Condición A');
$I->selectOption('TaxCondition[document_type_id]', 'DNI');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_tax_condition_view');

$I->see('Condición A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_tax_condition_update');

$I->fillField('TaxCondition[name]', 'Condición A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Condición A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Condición A *');
