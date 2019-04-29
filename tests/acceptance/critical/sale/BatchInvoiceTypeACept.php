<?php

$formatter = Yii::$app->formatter;

$I = new WebGuy($scenario);
$I->wantTo('ensure that batch invoice works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->see('Facturación por Lotes', 'h1');

$I->selectOption('ContractSearch[company_id]', 'ACME');
$I->selectOption('ContractSearch[bill_type_id]', 'Factura A');
$I->fillField('ContractSearch[period]', date("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->makeScreenshot('sale_batch_invoice_contracts');

$I->dontSee('No se encontraron resultados.');

$I->see('Juan', 'td');
$I->see('Ana', 'td');
$I->see('César', 'td');

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado', 30);

$I->click('Buscar Contratos');
$I->wait(1);

$I->dontSee('Juan', 'td');
$I->dontSee('Ana', 'td');
$I->dontSee('César', 'td');

// Verifico comprobantes creados

$I->clickMainMenu('Comprobantes', 'Lista: Facturas A');

$I->see('Juan', 'td');
/* Juan posee dos contratos,
 * uno al inicio del mes, por lo tanto se cobra 29 de 30 días del mes de septiembre
 * otro al día 25 del mes, por lo tanto se cobra 30 días del mes siguiente en forma adelantada
 */
$currentMonthContract = 100 * 29 / 30;
$nextMonthContract = 600;
$expectedBill = $currentMonthContract + $nextMonthContract;
$I->see($formatter->asCurrency($expectedBill * 1.21 /* precio lista + IVA */ * 1 /* categoría normal */), 'td');

$I->see('Ana', 'td');
$expectedBill = 3000 * 29 / 30;
$I->see($formatter->asCurrency($expectedBill * 1.21 /* precio lista + IVA */ * 0.75 /* categoría VIP */), 'td');

$I->see('César', 'td');
$expectedBill = (600 + 100) * 29 / 30;
$I->see($formatter->asCurrency($expectedBill  * 1.21 /* precio lista + IVA */ * 0.25 /* categoría mantenimiento */), 'td');

// Verifico el proceso del mes siguiente

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->selectOption('ContractSearch[company_id]', 'ACME');
$I->selectOption('ContractSearch[bill_type_id]', 'Factura A');
$nextMonth = new \DateTime("today +1 month");
$I->fillField('ContractSearch[period]', $nextMonth->format("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado', 30);

// Verifico el proceso del mes siguiente y el siguiente

$I->selectOption('ContractSearch[company_id]', 'ACME');
$I->selectOption('ContractSearch[bill_type_id]', 'Factura A');
$nextMonth = new \DateTime("today +2 month");
$I->fillField('ContractSearch[period]', $nextMonth->format("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado', 30);

$I->clickMainMenu('Comprobantes', 'Lista: Facturas A');

$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2("BillSearch[customer_id]", 'Juan');
$I->click('Buscar');
$I->wait(1);

$expectedBill = (100 *  29 / 30 + 600) + (100 + 0 ) + (100 + 600);
$I->see($formatter->asCurrency($expectedBill * 1.21 /* precio lista + IVA */ * 1 /* categoría normal */), 'td');


// Limpio los comprobantes creados

TestDbHelper::cleanBills();

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->selectOption('ContractSearch[company_id]', 'ACME');
$I->selectOption('ContractSearch[bill_type_id]', 'Factura A');
$I->fillField('ContractSearch[period]', date("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->see('Juan', 'td');
$I->see('Ana', 'td');
$I->see('César', 'td');

