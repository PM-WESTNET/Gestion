<?php

$random = rand(111, 999);
$name = "Period $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Accounting Period CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", "Periodos Contables");
$I->makeScreenshot('accounting_accounting_period_index');

$I->click('Alta Periodo Contable');

$I->makeScreenshot('accounting_accounting_period_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('AccountingPeriod[name]', $name);
$I->fillField('AccountingPeriod[number]', 'BAD');
$I->click('Alta');
$I->wait(1);
$I->see('Numero debe ser un número entero.');

$I->fillField('AccountingPeriod[number]', '123');
$I->wait(1);
$I->fillField('AccountingPeriod[date_from]', '32-15-2100');
$I->fillField('AccountingPeriod[date_to]', '32-15-2100');
$I->click('Alta');
$I->wait(1);

$I->see('El formato de Fecha Desde es inválido.');
$I->see('El formato de Fecha Hasta es inválido.');

// Verifico comportamiento correcto

$I->fillField('AccountingPeriod[date_from]', '01-11-2015');
$I->fillField('AccountingPeriod[date_to]', '30-11-2015');
$I->selectOption('AccountingPeriod[status]', "Abierto");
$I->wait(1);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('accounting_accounting_period_view');

$I->see($name, 'h1');
$I->click('Actualizar');

$I->makeScreenshot('accounting_accounting_period_update');

$name .= ' *';

$I->fillField('AccountingPeriod[name]', $name);
$I->click('Actualizar');
$I->wait(1);

$I->see($name, 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($random);

// Reopen last period

TestDbHelper::execute("UPDATE `accounting_period` set `status` = 'open' where `accounting_period_id` = 2");