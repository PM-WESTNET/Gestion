<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works, Pedido -> Remito -> Factura A');

 $I->loginAsUser();

$initial_stocks = $I->getStocks('manzana');

// Inicio comprobante
$I->clickMainMenu('Facturación', 'Pedido');
if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('Order[company_id]', 'ACME');
}
$I->wait(1); // only for selenium
$I->selectOption('Order[bill_type_id]', 'Pedido');

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
$I->fillField('BillDetail[secondary_qty]', '1');
$I->pressKey("//input[@name='BillDetail[secondary_qty]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField('BillDetail[unit_net_price]', '3.2');
$I->pressKey("//input[@name='BillDetail[unit_net_price]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

// Verifico cálculo correcto
$I->see(Yii::$app->formatter->asCurrency(6.4), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency(1.34), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency(7.74), 'td'); // Total
$I->click('Cerrar');
$I->wait(1); // only for selenium

// Verifico mensaje de éxito
$I->dontSee('error');
$I->see('Cerrado');

// Verifico valor final de stock
$final_stocks = $I->getStocks('manzana');
assert($initial_stocks['company_primary'] == $final_stocks['company_primary']);
assert($initial_stocks['company_secondary'] == $final_stocks['company_secondary']);
assert($initial_stocks['available_primary'] == $final_stocks['available_primary'] + 2);
assert($initial_stocks['available_secondary'] == $final_stocks['available_secondary'] + 1);

// Inicio Remito

$I->clickMainMenu("Comprobantes", "Pedido");
$I->click("//a[@title='Ver']");
$I->click("//a[contains(text(), 'Remito') and contains(@class, 'btn')]");
$I->wait(1); // only for selenium

$I->seeInField('BillDetail[qty]', '2');
$I->seeInField('BillDetail[secondary_qty]', '1');
$I->seeInField('BillDetail[unit_net_price]', Yii::$app->formatter->asDecimal(3.2));
$I->see(Yii::$app->formatter->asCurrency(6.4), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency(1.34), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency(7.74), 'td'); // Total

$I->click('Cerrar');
$I->wait(1); // only for selenium

$I->dontSee('error');
$I->see('Cerrado');

// Verifico valor final de stock
$final_stocks = $I->getStocks('manzana');
assert($initial_stocks['company_primary'] == $final_stocks['company_primary'] + 2);
assert($initial_stocks['company_secondary'] == $final_stocks['company_secondary'] + 1);
assert($initial_stocks['available_primary'] == $final_stocks['available_primary'] + 2);
assert($initial_stocks['available_secondary'] == $final_stocks['available_secondary'] + 1);

// Inicio Factura A

$I->clickMainMenu("Comprobantes", "Remito");
$I->click("//a[@title='Ver']");
$I->click("//a[contains(text(), 'Factura A') and contains(@class, 'btn')]");
$I->wait(1); // only for selenium

$I->see('2 kg');
$I->see('1 u');
$I->see('$3,20');
$I->see('$6,40', 'td'); // Subtotal
$I->see('$1,34', 'td'); // IVA
$I->see('$7,74', 'td'); // Total

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->see('Pedido');
$I->see('Remito');
$I->see('Factura A');

// Verifico valor final de stock
$final_stocks = $I->getStocks('manzana');
assert($initial_stocks['company_primary'] == $final_stocks['company_primary'] + 2);
assert($initial_stocks['company_secondary'] == $final_stocks['company_secondary'] + 1);
assert($initial_stocks['available_primary'] == $final_stocks['available_primary'] + 2);
assert($initial_stocks['available_secondary'] == $final_stocks['available_secondary'] + 1);
