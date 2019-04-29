<?php

$key = 'Resume test';
$random = rand(1000, 9999);
$resume_name = "$key #$random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Resume CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Contabilidad", 'Resúmenes bancarios');

$I->makeScreenshot('accounting_resume_index');

$I->click('Alta Resumen bancario');

$I->makeScreenshot('accounting_resume_create');

// Verifico mensaje de campos requeridos

$I->click('Siguiente');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Resume[date]', '07-10-2015');
$I->fillField('Resume[date_from]', '01-10-2015');
$I->fillField('Resume[date_to]', '31-10-2015');
$I->selectOption('Resume[money_box_id]', 'Banco Regional de Cuyo');
$I->wait(1);
$I->selectOption('Resume[money_box_account_id]', '1234');
$I->fillField('Resume[name]', $resume_name);
$I->fillField('Resume[balance_initial]', '1000');
$I->fillField('Resume[balance_final]', '2000');

$I->click('Siguiente');
$I->wait(1);

$I->see('Detalle del Resumen');

// Ver resumen antes de agregar detalle

$I->click('Banco Regional de Cuyo - 1234 - ' . $resume_name);
$I->wait(1);

$I->makeScreenshot('accounting_resume_view');

$I->see($resume_name);
$I->see('Banco Regional de Cuyo - 1234');
$I->see('07-10-2015');
$I->see('01-10-2015');
$I->see('31-10-2015');
$I->see('$1.000,00');
$I->see('$2.000,00');
$I->see('borrador');

// Edito

$I->click('Actualizar');
$I->wait(1);

$I->makeScreenshot('accounting_resume_update');

$resume_name .= ' edited';
$I->fillField('Resume[name]', $resume_name);

$I->click('Actualizar');
$I->wait(1);

$I->see($resume_name);

// Cargo detalle

$I->click('Editar Detalle');
$I->wait(1);

$I->makeScreenshot('accounting_resume_detail');

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

$I->selectOption('ResumeItem[operation_type_id]', 'Transferencia a tercero');
$I->wait(1);
$I->fillField('ResumeItem[description]', 'Salida por error');
$I->fillField('ResumeItem[date]', '04-10-2015');
$I->fillField('ResumeItem[debit]', '123');
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

// Elimino detalle erroneo

$I->click("(//a[@title='Eliminar'])[3]");
$I->acceptPopup();
$I->wait(1);

$I->see('$900,00');
$I->see('$1.900,00');

$I->click('Listo');
$I->acceptPopup();
$I->wait(1);

$I->see('El Resumen ha sido cerrado.');

// Eliminar está deshabilitado, se elimina manualmente
TestDbHelper::execute("DELETE FROM `resume_item` where `resume_id` in (SELECT `resume_id` FROM `resume` where `name` like '%$key%')");
TestDbHelper::execute("DELETE FROM `resume` where `name` like '%$key%'");

$I->clickMainMenu("Contabilidad", 'Resúmenes bancario');
$I->dontSee($resume_name);
