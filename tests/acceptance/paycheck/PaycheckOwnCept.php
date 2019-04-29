<?php
// En esta prueba considera la existencia de un tipo de entidad llamada banco

$I = new WebGuy($scenario);
$I->wantTo('ensure that Paycheck Own CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad",'Cheques');

$I->makeScreenshot('paycheck_paycheck_index');

$I->click('Alta Cheque');

$I->makeScreenshot('paycheck_paycheck_create');

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

$I->click(".//*[@id='paycheck-is_own']");
$I->wait(1);
$I->click(".//*[@id='paycheck-to_order']");
$I->click(".//*[@id='paycheck-crossed']");
$I->selectOption('Paycheck[money_box_id]', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Paycheck[money_box_account_id]', '1234');
$I->wait(1);
$I->selectOption('Paycheck[checkbook_id]', '1');
$I->fillField('Paycheck[date]', '01-12-2015');
$I->fillField('Paycheck[due_date]', '31-12-2015');
$I->fillField('Paycheck[number]', '12345');
$I->fillField('Paycheck[amount]', '123.45');
$I->fillField('Paycheck[description]', '#test#');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('paycheck_paycheck_view');

$I->see('Cheque - Propio - Banco Regional de Cuyo - 1234', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('paycheck_paycheck_update');

$random = rand(1111, 9999);

$I->fillField('Paycheck[number]', $random);
$I->click('Actualizar');
$I->wait(1);

$I->see($random);

TestDbHelper::execute("DELETE FROM `paycheck` where `description` like '#test#'");

$I->clickMainMenu("Contabilidad",'Cheques');
$I->dontSee($random);