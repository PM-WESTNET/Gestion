<?php
// En esta prueba considera la existencia de un tipo de entidad llamada banco

$number = rand(11111,9999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Paycheck Third party CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad",'Cheques');

$I->click('Alta Cheque');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Importe no puede estar vacío.');
$I->see('Número no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('Paycheck[number]', 'BAD');
$I->fillField('Paycheck[amount]', 'BAD');

$I->click('Alta');
$I->wait(1);
$I->see('Importe debe ser un número.');
$I->see('Número debe ser mayor o igual a "0".');

// Verifico comportamiento correcto

$I->click(".//*[@id='paycheck-to_order']");
$I->click(".//*[@id='paycheck-crossed']");
$I->selectOption('Paycheck[money_box_id]', 'Banco Regional de Cuyo');
$I->selectOption('Paycheck[money_box_account_id]', '123');
$I->selectOption('Paycheck[checkbook_id]', 'Desde: 1 - Hasta: 100 - Ultimo Usado: 0');
$I->fillField('Paycheck[document_number]', '63-85967423-1');
$I->fillField('Paycheck[business_name]', 'Quoma S.A.');
$I->fillField('Paycheck[date]', '01-12-2015');
$I->fillField('Paycheck[due_date]', '31-12-2015');
$I->fillField('Paycheck[number]', $number);
$I->fillField('Paycheck[amount]', '123.45');
$I->fillField('Paycheck[description]', '#test#');
$I->click('Alta');
$I->wait(1);

$I->see('Cheque - De Tercero - Quoma S.A.', 'h1');
$I->click('Actualizar');

$random = rand(1111, 9999);

$I->fillField('Paycheck[number]', $random);
$I->click('Actualizar');
$I->wait(1);

$I->see($random);

TestDbHelper::execute("DELETE FROM `paycheck` where `description` like '#test#'");

$I->clickMainMenu("Contabilidad",'Cheques');
$I->dontSee($random);