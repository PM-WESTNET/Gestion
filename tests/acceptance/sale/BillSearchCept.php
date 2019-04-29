<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Bill Search works');

$I->loginAsAdmin();

$I->clickMainMenu("Comprobantes", "Todos los comprobantes");

function fillDateFields($I, $fromDate, $toDate)
{
    $I->click('Filtros');
    $I->wait(1);
    $I->fillField("BillSearch[fromDate]", $fromDate);
    $I->fillField("BillSearch[toDate]", $toDate);
    $I->click('Buscar');
    $I->wait(1);
}

function fillAmountFields($I, $fromAmount, $toAmount)
{
    $I->click('Filtros');
    $I->wait(1);
    $I->fillField("BillSearch[fromAmount]", $fromAmount);
    $I->fillField("BillSearch[toAmount]", $toAmount);
    $I->click('Buscar');
    $I->wait(1);
}

// Verifico valores iniciales

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

// Verifico filtrado por fechas

fillDateFields($I, '01-01-2014', '');

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

fillDateFields($I, '', '31-12-2016');

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

fillDateFields($I, '01-01-2014', '31-12-2016');

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

fillDateFields($I, '15-03-2015', '');

$I->dontSee('01-01-2015');
$I->dontSee('01-02-2015');
$I->dontSee('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

fillDateFields($I, '', '15-03-2015');

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->dontSee('01-04-2015');
$I->dontSee('01-05-2015');
$I->dontSee('01-06-2015');

fillDateFields($I, '15-03-2015', '16-03-2015');

$I->see('No se encontraron resultados.');

// Verifico filtrado por montos

$I->clickMainMenu("Comprobantes", "Todos los comprobantes");
$I->wait(1);

fillAmountFields($I, '0', '');

$I->dontSee('No se encontraron resultados.');

fillAmountFields($I, '', '10000');

$I->dontSee('No se encontraron resultados.');

fillAmountFields($I, '2000', '');

$I->dontSee('No se encontraron resultados.');

$I->dontSee('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->dontSee('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');

fillAmountFields($I, '', '2000');

$I->dontSee('No se encontraron resultados.');

$I->see('01-01-2015');
$I->dontSee('01-02-2015');
$I->dontSee('01-03-2015');
$I->see('01-04-2015');
$I->dontSee('01-05-2015');
$I->dontSee('01-06-2015');

fillAmountFields($I, '2000', '3000');

$I->dontSee('No se encontraron resultados.');

$I->dontSee('01-01-2015');
$I->see('01-02-2015');
$I->dontSee('01-03-2015');
$I->dontsee('01-04-2015');
$I->see('01-05-2015');
$I->dontSee('01-06-2015');

// Verifico filtros por estado

$I->clickMainMenu("Comprobantes", "Todos los comprobantes");
$I->wait(1);

$I->click('Filtros');
$I->wait(1);
$I->checkOption("//input[@type='checkbox' and @value='draft']");
$I->click('Buscar');
$I->wait(1);

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->dontSee('01-04-2015');
$I->dontSee('01-05-2015');
$I->dontSee('01-06-2015');
$I->see('Borrador', 'td');
$I->dontSee('Completo', 'td');
$I->dontSee('Cerrado', 'td');

$I->click('Filtros');
$I->wait(1);
$I->uncheckOption("//input[@type='checkbox' and @value='draft']");
$I->checkOption("//input[@type='checkbox' and @value='completed']");
$I->click('Buscar');
$I->wait(1);

$I->dontSee('01-01-2015');
$I->dontSee('01-02-2015');
$I->dontSee('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');
$I->dontSee('Borrador', 'td');
$I->see('Completo', 'td');
$I->dontSee('Cerrado', 'td');

$I->click('Filtros');
$I->wait(1);
$I->checkOption("//input[@type='checkbox' and @value='draft']");
$I->checkOption("//input[@type='checkbox' and @value='completed']");
$I->click('Buscar');
$I->wait(1);

$I->see('01-01-2015');
$I->see('01-02-2015');
$I->see('01-03-2015');
$I->see('01-04-2015');
$I->see('01-05-2015');
$I->see('01-06-2015');
$I->see('Borrador', 'td');
$I->see('Completo', 'td');
$I->dontSee('Cerrado', 'td');

$I->click('Filtros');
$I->wait(1);
$I->uncheckOption("//input[@type='checkbox' and @value='draft']");
$I->uncheckOption("//input[@type='checkbox' and @value='completed']");
$I->checkOption("//input[@type='checkbox' and @value='closed']");
$I->click('Buscar');
$I->wait(1);

$I->dontSee('Borrador', 'td');
$I->dontSee('Completo', 'td');
$I->see('Cerrado', 'td');

// Verifico por tipo de comprobante

$I->clickMainMenu("Comprobantes", "Todos los comprobantes");
$I->wait(1);

$I->click('Filtros');
$I->wait(1);
$I->checkOption("//input[@type='checkbox' and @value='1' and @name='BillSearch[bill_types][]']");
$I->click('Buscar');
$I->wait(1);

$I->see('Factura A', 'td');
$I->dontSee('Factura B', 'td');

$I->click('Filtros');
$I->wait(1);
$I->uncheckOption("//input[@type='checkbox' and @value='1' and @name='BillSearch[bill_types][]']");
$I->checkOption("//input[@type='checkbox' and @value='2' and @name='BillSearch[bill_types][]']");
$I->click('Buscar');
$I->wait(1);

$I->dontsee('Factura A', 'td');

// Verifico filtro por cliente

$I->clickMainMenu("Comprobantes", "Todos los comprobantes");
$I->wait(1);

$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2("BillSearch[customer_id]", 'Juan');
$I->click('Buscar');
$I->wait(1);

$I->see('Juan Garcia', 'td');
$I->dontSee('José Gómez', 'td');

$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2("BillSearch[customer_id]", 'José');
$I->click('Buscar');
$I->wait(1);

$I->dontSee('Juan Garcia', 'td');

$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2("BillSearch[customer_id]", 'gar');
$I->click('Buscar');
$I->wait(1);

$I->see('Juan Garcia', 'td');
$I->dontSee('José Gómez', 'td');

$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2("BillSearch[customer_id]", 'gom');
$I->click('Buscar');
$I->wait(1);

$I->dontSee('Juan Garcia', 'td');
