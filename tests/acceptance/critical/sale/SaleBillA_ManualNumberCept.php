<?php

$billNumber = rand(1111,9999);

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works, Factura A');

$I->loginAsSuperadmin();

$I->amGoingTo('try to pay a invoice with no client');
$I->clickMainMenu('Facturación', 'Factura A');

$I->makeScreenshot('sale_bill_create_init');

$url = $I->grabFromCurrentUrl();
$id = explode('id=', $url)[1];

if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('Bill[company_id]', 'ACME');
}
$I->wait(1); // only for selenium
$I->selectOption('Bill[bill_type_id]', 'Factura A');

// Selecciono cliente
$I->fillField('#customer_search', 'Juan');
$I->click("//a[contains(@onclick, '.search')]");
$I->wait(1);

$I->makeScreenshot('sale_bill_customer_search');

$I->click('Seleccionar');
$I->wait(1);

// Select manual point of sale

$I->selectOption('Bill[point_of_sale_id]', 'Manual - 2');
$I->wait(1);

$I->fillField('Bill[number]', $billNumber);
$I->wait(1);

$I->fillField('Bill[observation]', 'obs');
$I->wait(1);

// Busco y agrego producto
$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_bill_product_search');

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

$I->makeScreenshot('sale_bill_create_final');

// Cerrar

$I->click('Aceptar');
$I->waitForText('El comprobante fue registrado exitosamente.', 20);

$I->dontSee('Error');
$I->dontSee('error');

$I->see($billNumber);

// Pago
$I->click('Pagar');
$I->wait(1); // only for selenium
$I->see('Alta Pago', 'h1');
$I->selectOption("PaymentItem[payment_method_id]", 'Contado');
$I->fillField('PaymentItem[amount]', '7.74');
$I->click('Agregar');
$I->wait(1); // only for selenium
$I->click('Guardar');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_bill_view');

// Verifico mensaje de éxito
$I->dontSee('error');
$I->see('Venta');
$I->see('Pagado');

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_bill_history');

$I->see('Factura A');

// Verifico contabilidad

$I->clickMainMenu('Contabilidad', 'Libro Diario');

try {
    $I->click(".//span[@class = 'glyphicon glyphicon-fast-forward']/..");
} catch (\Exception $e) {

}

$I->see(Yii::$app->formatter->asCurrency(6.4), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency(1.34), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency(7.74), 'td'); // Total
