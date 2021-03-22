<?php

$formatter = Yii::$app->formatter;

$I = new WebGuy($scenario);
$I->wantTo('ensure that batch invoice works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->see('Facturación por Lotes', 'h1');

// Verifico filtros mal ingresados

$I->selectOption('ContractSearch[company_id]', 'ACME');
$I->selectOption('ContractSearch[bill_type_id]', 'Factura A');
$I->fillField('ContractSearch[period]', date("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->dontSee('No se encontraron resultados.');

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado');

// Process next month

$nextMonth = (new \DateTime(date('Y-m') . '-10'))->modify('+1 month')->format('d-m-Y');
$I->fillField('ContractSearch[period]', $nextMonth);

$I->click('Buscar Contratos');
$I->wait(1);

$I->dontSee('No se encontraron resultados.');

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado');

// Verifico comprobantes creados

$I->clickMainMenu('Comprobantes', 'Lista: Facturas A');

$I->click('Filtros');
$I->wait(1);

$I->selectOptionForSelect2('BillSearch[customer_id]', 'Ana');
$I->click('Buscar');

$I->see('Ana', 'td');
$I->see($formatter->asCurrency((3000 /* precio lista */ * 0.75 /* categoría VIP */ - 16 /* descuento fijo */) * 1.21 /* IVA */), 'td');
$I->see($formatter->asCurrency((3000 /* precio lista */ * 0.75 /* categoría VIP */) * 1.21 /* IVA */), 'td');

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

