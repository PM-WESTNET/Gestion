<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works, Presupuesto -> Factura A');

 $I->loginAsUser();

$apple_initial_stocks = $I->getStocks('manzana');
$I->comment('Apple primary = ' . $apple_initial_stocks['company_primary']);
$I->comment('Apple secondary ' . $apple_initial_stocks['company_secondary']);
$pear_initial_stocks = $I->getStocks('pera');
$I->comment('Pear primary = ' . $pear_initial_stocks['company_primary']);

do {
    $apple_quantity = rand(10, 99);
} while ($apple_quantity % 10 == 0);
$apple_quantity = $apple_quantity / 10;
$apple_2nd_quantity = rand(1, 9);

do {
    $pear_quantity = rand(10, 99);
} while ($pear_quantity % 10 == 0);
$pear_quantity = $pear_quantity / 10;

$apple_price = rand(20, 99) / 10;
$pear_price = rand(20, 99) / 10;

$apple_total = round($apple_quantity * $apple_price, 2);
$pear_total = round($pear_quantity * $pear_price, 2);

$bill_subtotal = $apple_total + $pear_total;
$bill_tax = round($bill_subtotal * 0.21, 2);
$bill_total = round($bill_subtotal * 1.21, 2);

// Inicio comprobante
$I->clickMainMenu('Facturación', 'Presupuesto');

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
$I->fillField("//input[@id='input-qty0']", $apple_quantity);
$I->pressKey("//input[@id='input-qty0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField("//input[@id='input-secondary-qty0']", $apple_2nd_quantity);
$I->pressKey("//input[@id='input-secondary-qty0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField("//input[@id='input-prc0']", $apple_price);
$I->pressKey("//input[@id='input-prc0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'pera');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->click('Agregar');
$I->wait(1); // only for selenium
// Defino valores de compra
$I->fillField("//input[@id='input-qty1']", $pear_quantity);
$I->pressKey("//input[@id='input-qty1']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField("//input[@id='input-prc1']", $pear_price);
$I->pressKey("//input[@id='input-prc1']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
// Verifico cálculo correcto
$I->see(Yii::$app->formatter->asCurrency($apple_total), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($pear_total), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($bill_subtotal), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($bill_tax), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency($bill_total), 'td'); // Total
$I->click('Cerrar');
$I->wait(1); // only for selenium
// Verifico mensaje de éxito
$I->dontSee('error');
$I->see('Cerrado');

// Verifico valor final de stock
$apple_final_stocks = $I->getStocks('manzana');
$I->comment('see ' . $apple_final_stocks['company_primary']);
assert($apple_initial_stocks['company_primary'] == $apple_final_stocks['company_primary']);
$I->comment('see ' . $apple_initial_stocks['company_secondary']);
assert($apple_initial_stocks['company_secondary'] == $apple_final_stocks['company_secondary']);
assert($apple_initial_stocks['available_primary'] == $apple_final_stocks['available_primary']);
assert($apple_initial_stocks['available_secondary'] == $apple_final_stocks['available_secondary']);

$pear_final_stocks = $I->getStocks('pera');
assert($pear_initial_stocks['company_primary'] == $pear_final_stocks['company_primary']);
assert($pear_initial_stocks['available_primary'] == $pear_final_stocks['available_primary']);

// Inicio Factura A

$I->clickMainMenu("Comprobantes", "Presupuesto");
$I->click("//a[@title='Ver']");
$I->click("//a[contains(text(), 'Factura A') and contains(@class, 'btn')]");
$I->wait(1); // only for selenium

$I->seeInField('BillDetail[qty]', $apple_quantity);
$I->seeInField('BillDetail[secondary_qty]', $apple_2nd_quantity);
$I->seeInField('BillDetail[unit_net_price]', Yii::$app->formatter->asDecimal($apple_price));
$I->see(Yii::$app->formatter->asCurrency($apple_total), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($pear_total), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($bill_subtotal), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency($bill_tax), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency($bill_total), 'td'); // Total

$I->click('Cerrar');
$I->wait(1); // only for selenium

$I->dontSee('error');
$I->see('Cerrado');

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->see('Presupuesto');
$I->see('Factura A');

// Verifico valor final de stock
$apple_final_stocks = $I->getStocks('manzana');
$I->comment('see ' . ($apple_final_stocks['company_primary'] + $apple_quantity));
assert($apple_initial_stocks['company_primary'] == $apple_final_stocks['company_primary'] + $apple_quantity);
$I->comment('see ' . ($apple_final_stocks['company_secondary'] + $apple_2nd_quantity));
assert($apple_initial_stocks['company_secondary'] == $apple_final_stocks['company_secondary'] + $apple_2nd_quantity);
$I->comment('see ' . ($apple_final_stocks['company_primary'] + $apple_quantity));
assert($apple_initial_stocks['available_primary'] == $apple_final_stocks['available_primary'] + $apple_quantity);
$I->comment('see ' . ($apple_final_stocks['company_secondary'] + $apple_2nd_quantity));
assert($apple_initial_stocks['available_secondary'] == $apple_final_stocks['available_secondary'] + $apple_2nd_quantity);

$pear_final_stocks = $I->getStocks('pera');
assert($pear_initial_stocks['company_primary'] == $pear_final_stocks['company_primary'] + $pear_quantity);
assert($pear_initial_stocks['available_primary'] == $pear_final_stocks['available_primary'] + $pear_quantity);
