<?php

$payment = rand(11111, 99999) / 100;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Payment CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Clientes", "Pagos");

$I->makeScreenshot('checkout_payment_index');

$I->click('Alta Pago');

$I->makeScreenshot('checkout_payment_create');

// Verifico mensaje de campos requeridos

$I->click('Siguiente');
$I->wait(1);
$I->see('Importe no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('Payment[amount]', 'BAD');
$I->click('Siguiente');
$I->wait(1);
$I->see('Importe debe ser un número.');

// Verifico comportamiento correcto

$I->selectOption('Payment[company_id]', 'ACME');
$I->fillField('Payment[date]', date('d-m-Y'));
$I->selectOptionForSelect2('Payment[customer_id]', 'Juan');
$I->fillField('Payment[amount]', $payment);
$I->click('Siguiente');
$I->wait(1);

$I->makeScreenshot('checkout_payment_update');

$I->see('Medio de Pago');

$I->fillField('PaymentItem[amount]', $payment);
$I->click('Agregar');
$I->wait(3);

$I->click('Aceptar');
$I->acceptPopup();
$I->wait(1);

$I->makeScreenshot('checkout_payment_view');

$I->see('Cerrado');

$I->clickMainMenu("Clientes", "Pagos");

$I->wait(1);
$I->click('Filtros');
$I->wait(1);

$I->fillField('PaymentSearch[from_amount]', $payment);
$I->fillField('PaymentSearch[to_amount]', $payment);

$I->click('Filtrar');

$I->waitForText(Yii::$app->formatter->asCurrency($payment));
