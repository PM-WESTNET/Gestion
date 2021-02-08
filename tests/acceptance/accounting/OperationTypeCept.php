<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Operation Type CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", 'Tipos de Operaciones');

$I->makeScreenshot('accounting_operation_type_index');

$I->click('Alta Tipo de Operación');

$I->makeScreenshot('accounting_operation_type_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('OperationType[name]', 'Operación A');
$I->fillField('OperationType[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_operation_type_view');

$I->see('Operación A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('accounting_operation_type_update');

$I->fillField('OperationType[name]', 'Operación A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Operación A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Operación A *');
