<?php

$quantity = rand(10, 20);
$price = rand(100, 200) / 10;
$rawTotal = $quantity * $price;
$netTotal = $rawTotal * 1.21; // $rawTotal + Tax
$payment = rand(100, 300) / 10;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Current Account works');

$I->loginAsSuperadmin();

// Buscamos estado inicial de cuenta corriente

$I->clickMainMenu("Clientes", "Clientes");
$I->selectOptionForSelect2('CustomerSearch[customer_id]', 'Juan');
$I->wait(1);
$I->click('Cuenta');
$I->wait(1);

$I->makeScreenshot('checkout_current_account');

// Tomo los valores en pantalla

$raw_credit = $I->grabTextFrom("html/body/div[1]/div/div[3]/table/tbody/tr[1]/td[2]/strong");
$initial_credit = FormatHelper::Getfloat($raw_credit);
$raw_debit = $I->grabTextFrom("html/body/div[1]/div/div[3]/table/tbody/tr[2]/td[2]/strong");
$initial_dedit = FormatHelper::Getfloat($raw_debit);

// Realizo venta

$I->clickMainMenu('Facturación', 'Factura A');

if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('Bill[company_id]', 'ACME');
}
$I->wait(1);
$I->selectOption('Bill[bill_type_id]', 'Factura A');

// Selecciono cliente
$I->fillField('#customer_search', 'Juan');
$I->click("//a[contains(@onclick, '.search')]");
$I->wait(1);
$I->click('Seleccionar');
$I->wait(1);

// Busco y agrego producto
$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1);
$I->click('Agregar');
$I->wait(1);
// Defino valores de compra
$I->fillField('BillDetail[qty]', $quantity);
$I->pressKey("//input[@name='BillDetail[qty]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->fillField('BillDetail[unit_net_price]', $price);
$I->pressKey("//input[@name='BillDetail[unit_net_price]']", WebDriverKeys::ENTER);
$I->wait(1);

$I->click('Aceptar');
$I->waitForText('Factura A - N', 20);

// Verifico presencia en cuenta corriente

$I->clickMainMenu("Clientes", "Clientes");
$I->selectOptionForSelect2('CustomerSearch[customer_id]', 'Juan');
$I->wait(1);
$I->click('Cuenta');
$I->wait(1);

$I->click("Alta Pago");

$I->fillField('Payment[amount]', 'BAD');
$I->click("Siguiente");
$I->wait(1);
$I->see("Importe debe ser un número.");

$I->fillField('Payment[amount]', $payment);
$I->click("Siguiente");
$I->wait(1);

$I->selectOption("PaymentItem[payment_method_id]", 'Contado');
$I->fillField('PaymentItem[amount]', $payment);
$I->click('Agregar');
$I->wait(1);
$I->click('Aceptar');
$I->acceptPopup();
$I->wait(1);

$I->clickMainMenu("Clientes", "Clientes");
$I->selectOptionForSelect2('CustomerSearch[customer_id]', 'Juan');
$I->wait(1);
$I->click('Cuenta');
$I->wait(1);

$raw_credit = $I->grabTextFrom("html/body/div[1]/div/div[3]/table/tbody/tr[1]/td[2]/strong");
$final_credit = FormatHelper::Getfloat($raw_credit);
$raw_debit = $I->grabTextFrom("html/body/div[1]/div/div[3]/table/tbody/tr[2]/td[2]/strong");
$final_dedit = FormatHelper::Getfloat($raw_debit);

$expected_credit = $initial_credit + $netTotal;
$expected_debit = $initial_dedit + $payment;
$expected_balance = $expected_debit - $expected_credit;

$I->expect("Values: $expected_credit, $expected_debit, $expected_balance");

$I->see(Yii::$app->formatter->asCurrency($expected_credit));
$I->see(Yii::$app->formatter->asCurrency($expected_debit));
$I->see(Yii::$app->formatter->asCurrency($expected_balance));

// Limpio los comprobantes creados

TestDbHelper::cleanBills();

