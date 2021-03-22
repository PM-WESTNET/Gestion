<?php

$formatter = Yii::$app->formatter;

$I = new WebGuy($scenario);
$I->wantTo('ensure that batch invoice works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->see('Facturación por Lotes', 'h1');

// Verifico filtros mal ingresados

$I->selectOption('ContractSearch[company_id]', 'Metro');
$I->wait(1);
$I->selectOption('ContractSearch[bill_type_id]', 'Factura B');
$I->fillField('ContractSearch[period]', date("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->dontSee('No se encontraron resultados.');

$I->see('José', 'td');

$I->click('Facturar');
$I->acceptPopup();
$I->waitForText('Proceso terminado', 30);

$I->click('Buscar Contratos');
$I->wait(1);

$I->dontSee('José', 'td');

// Verifico comprobantes creados

$I->clickMainMenu('Comprobantes', 'Lista: Facturas B');

$I->see('José', 'td');
// Conexión
$total = (600 * 29 / 30) * 1.21 /* precio lista + IVA */ * 1 /* categoría mantenimiento */;
// Router instalado
$total += 400 * 2 /* precio lista + IVA */ * 1 /* 1ra cuota router */;
$I->see($formatter->asCurrency($total), 'td');

// Limpio los comprobantes creados

TestDbHelper::cleanBills();

$I->clickMainMenu('Facturación', "Facturación por Lotes");

$I->selectOption('ContractSearch[company_id]', 'Metro');
$I->wait(1);
$I->selectOption('ContractSearch[bill_type_id]', 'Factura B');
$I->fillField('ContractSearch[period]', date("10-m-Y"));

$I->click('Buscar Contratos');
$I->wait(1);

$I->see('José', 'td');
