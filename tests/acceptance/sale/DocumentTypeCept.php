<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Document Type CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Clientes","Tipos de Doc.");

$I->makeScreenshot('sale_document_type_index');

$I->click('Alta Tipo de Doc.');

$I->makeScreenshot('sale_document_type_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Código no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('DocumentType[name]', 'Tipo de Doc. A');
$I->fillField('DocumentType[code]', '123');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_document_type_view');

$I->see('Tipo de Doc. A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_document_type_update');

$I->fillField('DocumentType[name]', 'Tipo de Doc. A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Tipo de Doc. A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Tipo de Doc. A *');