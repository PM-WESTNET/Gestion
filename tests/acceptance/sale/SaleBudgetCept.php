<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works, Presupuesto');

 $I->loginAsUser();

$initial_stocks = $I->getStocks('manzana');

// Inicio comprobante
$I->clickMainMenu('Facturación', 'Presupuesto');

$url = $I->grabFromCurrentUrl();
$id = split('id=', $url)[1];

if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('Budget[company_id]', 'ACME');
}
$I->wait(1); // only for selenium
$I->selectOption('Budget[bill_type_id]', 'Presupuesto');

// Selecciono cliente
$I->fillField('#customer_search', 'Juan');
$I->click("//a[contains(@onclick, '.search')]");
$I->wait(1);
$I->click('Seleccionar');
$I->wait(1);

// Busco y agrego producto
$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->click('Agregar');
$I->wait(1); // only for selenium
// Defino valores de compra
$I->fillField('BillDetail[qty]', '2');
$I->pressKey("//input[@name='BillDetail[qty]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField('BillDetail[unit_net_price]', '3.2');
$I->pressKey("//input[@name='BillDetail[unit_net_price]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
// Verifico cálculo correcto
$I->see(Yii::$app->formatter->asCurrency(6.4), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency(1.34), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency(7.74), 'td'); // Total
// Pago
$I->click('Cerrar');
$I->wait(1); // only for selenium

$I->see('Cerrado');

// Verifico número generado
$I->expect('number to be generated automatically');
$number = $I->grabTextFrom('html/body/div[1]/div/div[3]/div[3]/div[2]/div[2]/span[2]');
assert(isset($number) && $number != '');

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->see('Presupuesto');

// Verifico valor final de stock

$final_stocks = $I->getStocks('manzana');
assert($initial_stocks['company_primary'] == $final_stocks['company_primary']);
assert($initial_stocks['company_secondary'] == $final_stocks['company_secondary']);
assert($initial_stocks['available_primary'] == $final_stocks['available_primary']);
assert($initial_stocks['available_secondary'] == $final_stocks['available_secondary']);

// Verifico impresión
