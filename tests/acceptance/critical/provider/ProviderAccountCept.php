<?php

$number = rand(111, 999);
$payment = rand(11111, 99999) / 100;
$tax = round($payment * 0.21, 2);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Provider Account works');

 $I->loginAsSuperadmin();

// Buscamos estado inicial de cuenta corriente

$I->clickMainMenu("Proveedores", 'Proveedores');

$I->fillField('ProviderSearch[name]', 'Vea');
$I->pressKey(".//*[@name='ProviderSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->click("Cuenta");
$I->wait(1); // only for selenium

$I->makeScreenshot('paycheck_account_index');

// Tomo los valores en pantalla

$raw_bills = $I->grabTextFrom("//*[@id='total_bills']");
$initial_bills = FormatHelper::GetFloat($raw_bills);
$raw_payments = $I->grabTextFrom("//*[@id='total_payments']");
$initial_payments = FormatHelper::GetFloat($raw_payments);

// Cargo comprobante

$I->click("Alta Comprobante de proveedor");

// Carga de pago correcto

$I->fillField('ProviderBill[number]', $number);

$I->click("Agregar detalle");
$I->wait(1);

// Agrego item

$I->fillField('ProviderBillItem[description]', '#Test');
$I->fillField('ProviderBillItem[amount]', $payment);

$I->click('#item-add');
$I->wait(2);

$I->see(Yii::$app->formatter->asCurrency($payment));

// Agrego impuesto

$I->selectOption('ProviderBillHasTaxRate[tax_rate_id]', 'IVA - 21%');
$I->fillField('ProviderBillHasTaxRate[amount]', $tax);

$I->click('#tax-add');
$I->wait(2);

$I->see(Yii::$app->formatter->asCurrency($tax));

// Cargo pago

$I->click("Pagar");
$I->acceptPopup();
$I->wait(2);

$I->seeInField('ProviderPayment[date]', date("d-m-Y"));

$I->click("Siguiente");
$I->wait(1);

$I->selectOption('ProviderPaymentItem[payment_method_id]', 'Contado');
$I->selectOption('ProviderPaymentItem[money_box_id]', 'Westnet');
$I->selectOption('ProviderPaymentItem[money_box_account_id]', 'Centro');
$I->fillField('ProviderPaymentItem[description]', 'Pago');
$I->fillField('ProviderPaymentItem[amount]', $payment + $tax);

$I->see(Yii::$app->formatter->asCurrency($payment + $tax));

$I->click("Agregar");
$I->wait(1);

$I->checkOption('selection[]');

$I->click("Aceptar");
$I->acceptPopup();
$I->wait(1);

// Buscamos estado final de cuenta corriente

$I->clickMainMenu("Proveedores", 'Proveedores');

$I->fillField('ProviderSearch[name]', 'Vea');
$I->pressKey(".//*[@name='ProviderSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->click("Cuenta");
$I->wait(1); // only for selenium

$expected_bills = $initial_bills + $payment + $tax;
$expected_payments = $initial_payments + $payment + $tax;
$expected_balance = $expected_payments - $expected_bills;

$I->expect("Values: $expected_bills, $expected_payments, $expected_balance");

$formater = new \yii\i18n\Formatter();
$I->see($formater->asCurrency($expected_bills));
$I->see($formater->asCurrency($expected_payments));
$I->see($formater->asCurrency($expected_balance));
