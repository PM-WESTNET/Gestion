<?php

$key = 'test';
$random = rand(1000, 9999);
$resume_name = "Resumen $key $random";
$conciliation_name = "Conciliation $key $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Resume CRUD works');

$I->loginAsSuperadmin();

// Creo resumen temporal

$I->clickMainMenu("Contabilidad", 'Resúmenes bancario');

$I->click('Alta Resumen bancario');

$I->fillField('Resume[date_to]', '31-10-2015');
$I->fillField('Resume[date_from]', '01-10-2015');
$I->fillField('Resume[date]', '07-10-2015');
$I->selectOption('money_box_id', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Resume[money_box_account_id]', '1234');
$I->fillField('Resume[name]', $resume_name);
$I->fillField('Resume[balance_initial]', '1000');
$I->fillField('Resume[balance_final]', '2000');

$I->click('Siguiente');
$I->wait(1);

// Cargo detalle

$I->selectOption('ResumeItem[operation_type_id]', 'Transferencia de tercero');
$I->wait(1);
$I->fillField('ResumeItem[description]', 'Entrada 1');
$I->fillField('ResumeItem[date]', '03-10-2015');
$I->fillField('ResumeItem[credit]', '900');
$I->pressKey(".//*[@id='resumeitem-date']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click('Agregar');
$I->wait(1);

$I->selectOption('ResumeItem[operation_type_id]', 'Transferencia a tercero');
$I->wait(1);
$I->fillField('ResumeItem[description]', 'Salida 1');
$I->fillField('ResumeItem[date]', '02-10-2015');
$I->fillField('ResumeItem[debit]', '600');
$I->pressKey(".//*[@id='resumeitem-date']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click('Agregar');
$I->wait(1);

$I->selectOption('ResumeItem[operation_type_id]', 'Transferencia de tercero');
$I->wait(1);
$I->fillField('ResumeItem[description]', 'Entrada 2');
$I->fillField('ResumeItem[date]', '05-10-2015');
$I->fillField('ResumeItem[credit]', '1000');
$I->pressKey(".//*[@id='resumeitem-date']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click('Agregar');
$I->wait(1);

$I->selectOption('ResumeItem[operation_type_id]', 'Transferencia a tercero');
$I->wait(1);
$I->fillField('ResumeItem[description]', 'Salida 2');
$I->fillField('ResumeItem[date]', '05-10-2015');
$I->fillField('ResumeItem[debit]', '300');
$I->pressKey(".//*[@id='resumeitem-date']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click('Agregar');
$I->wait(1);

$I->click('Listo');
$I->acceptPopup();
$I->wait(1);

// Creo conciliación

$I->clickMainMenu("Contabilidad", 'Conciliaciones');

$I->makeScreenshot('accounting_conciliation_index');

$I->click('Alta Conciliación');

$I->makeScreenshot('accounting_conciliation_create');

$I->selectOption('money_box_id', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Conciliation[money_box_account_id]', '1234');
$I->wait(1);
$I->fillField('Conciliation[date]', '07-10-2015');
$I->fillField('Conciliation[date_from]', '01-10-2015');
$I->fillField('Conciliation[date_to]', '31-10-2015');
$I->selectOption('Conciliation[resume_id]', $resume_name);
$I->fillField('Conciliation[name]', $conciliation_name);
$I->wait(1);
$I->click('Siguiente');
$I->wait(1);

// Edito

$I->click($conciliation_name);

$I->click('Actualizar');
$I->wait(1);

$I->makeScreenshot('accounting_conciliation_update');

$conciliation_name .= ' edited';
$I->fillField('Conciliation[name]', $conciliation_name);

$I->click('Actualizar');
$I->wait(1);

$I->see($conciliation_name);

// Hago la conciliación

$I->click('Hacer');
$I->wait(1);

$I->makeScreenshot('accounting_conciliation_conciliate');

$I->click("(//input[@name='selection_all'])[4]"); // Selecciono todos los débitos
$I->click("(//input[@name='selection_all'])[5]"); // Selecciono todos los créditos
$I->wait(1);

$I->click('Conciliar');
$I->acceptPopup();
$I->wait(1);

$I->see('Transferencia a tercero');
$I->see('Transferencia de tercero');
$I->see('$900,00', 'td');
$I->see('$600,00', 'td');
$I->see('$1.000,00', 'td');
$I->see('$300,00', 'td');

$I->see($conciliation_name);
$I->see('07-10-2015');
$I->see('01-10-2015');
$I->see('31-10-2015');
$I->see('borrador');

$I->click('Listo');
$I->acceptPopup();
$I->wait(1);

// Eliminar está deshabilitado, se elimina manualmente
$consiliations = "SELECT `conciliation_id` FROM `conciliation` where `name` like '%$key%'";
TestDbHelper::execute("DELETE FROM `conciliation_item_has_resume_item` where `conciliation_item_id` in "
    . "(SELECT `conciliation_item_id` FROM `conciliation_item` where `conciliation_id` in ($consiliations))");
TestDbHelper::execute("DELETE FROM `conciliation_item` where `conciliation_id` in ($consiliations)");
TestDbHelper::execute("DELETE FROM `conciliation` where `name` like '%$key%'");

TestDbHelper::execute("DELETE FROM `resume_item` where `resume_id` in (SELECT `resume_id` FROM `resume` where `name` like '%$key%')");
TestDbHelper::execute("DELETE FROM `resume` where `name` like '%$key%'");

$I->clickMainMenu("Contabilidad", 'Resúmenes bancario');
$I->dontSee($conciliation_name);
