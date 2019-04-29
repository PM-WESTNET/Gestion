<?php

$random =  rand(111, 999);
$name = "Type $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Bill Type CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", 'Config', "Tipos de comprobante");

$I->makeScreenshot('sale_bill_type_index');

$I->click('Alta Tipo de comprobante');

$I->makeScreenshot('sale_bill_type_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Código no puede estar vacío.');
$I->see('Clase no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('BillType[code]', 'BAD');
$I->click('Alta');
$I->wait(1);
$I->see('Código debe ser un número entero.');

// Verifico comportamiento correcto

$I->fillField('BillType[name]', $name);
$I->fillField('BillType[code]', '123');
$I->selectOption('BillType[class]', 'app\modules\sale\models\bills\Bill');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_bill_type_view');

$I->see($name, 'h1');
$I->click('Actualizar');

$name .= ' *';

$I->makeScreenshot('sale_bill_type_update');

$I->fillField('BillType[name]', $name);
$I->click('Actualizar');
$I->wait(1);

$I->see($name, 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);
