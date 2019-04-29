<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$start_number = rand(1111, 9999);
$end_number = $start_number + 10;
$last_number = rand($start_number, $end_number);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Checkbook CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", 'Chequeras');

$I->makeScreenshot('paycheck_checkbook_index');

$I->click('Alta Chequera');

$I->makeScreenshot('paycheck_checkbook_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Número Inicial no puede estar vacío.');
$I->see('Número Final no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('Checkbook[start_number]', 'BAD');
$I->fillField('Checkbook[end_number]', 'BAD');
$I->fillField('Checkbook[last_used]', 'BAD');

$I->click('Alta');
$I->wait(1);
$I->see('Número Inicial debe ser un número entero.');
$I->see('Número Final debe ser un número entero.');
$I->see('Ultimo usado debe ser un número entero.');

// Verifico comportamiento correcto

$I->selectOption('Checkbook[money_box_id]', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Checkbook[money_box_account_id]', '1234');
$I->fillField('Checkbook[start_number]', $start_number);
$I->fillField('Checkbook[end_number]', $end_number);
$I->fillField('Checkbook[last_used]', '');
$I->click(".//*[@id='checkbook-enabled']");

$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('paycheck_checkbook_view');

$I->see('Chequera Banco Regional de Cuyo - 1234', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('paycheck_checkbook_update');

$I->fillField('Checkbook[last_used]', $last_number);

$I->click('Actualizar');
$I->wait(1);

$I->see($last_number);

// Intento crear una chequera con mismo rango

$I->clickMainMenu("Contabilidad", 'Chequeras');

$I->click('Alta Chequera');

$I->selectOption('Checkbook[money_box_id]', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Checkbook[money_box_account_id]', '1234');
$I->fillField('Checkbook[start_number]', $start_number);
$I->fillField('Checkbook[end_number]', $end_number);
$I->fillField('Checkbook[last_used]', $last_number);
$I->click(".//*[@id='checkbook-enabled']");

$I->click('Alta');
$I->wait(1);

$I->see('Ya existe una chequera con el rango seleccionado');

$I->clickMainMenu("Contabilidad", 'Chequeras');
$I->wait(1);

$I->seeNumberOfElements("//td[text() = '$last_number']", 1);

// Elimino manualmente

TestDbHelper::execute("DELETE FROM `checkbook` where `last_used` = '$last_number'");

$I->clickMainMenu("Contabilidad", 'Chequeras');
$I->dontSee($last_number, 'td');
