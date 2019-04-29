<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works, Factura A detalle manual');

 $I->loginAsSuperadmin();

$I->amGoingTo('try to pay a invoice with no client');
$I->clickMainMenu('Facturación', 'Factura A');

// Selecciono cliente
$I->fillField('#customer_search', 'Juan');
$I->click("//a[contains(@onclick, '.search')]");
$I->wait(1);
$I->click('Seleccionar');
$I->wait(1);

// Verifico mensaje de campos mal ingresados

$I->fillField('BillDetailForm[qty]', 'BAD');
$I->fillField('BillDetailForm[unit_net_price]', 'BAD');
$I->click('#handwrite-detail-add');
$I->wait(1);
$I->see('Cant. debe ser un número');
$I->see('Precio neto p/unid. debe ser un número');

// Agrego producto manual
$I->fillField('BillDetailForm[qty]', '3');
$I->fillField('BillDetailForm[concept]', 'Mi producto');
$I->fillField('BillDetailForm[unit_net_price]', '12.3');
$I->click('#handwrite-detail-add');
$I->wait(1); // only for selenium

// Verifico cálculo correcto
$I->see(Yii::$app->formatter->asCurrency(36.9), 'td'); // Subtotal
$I->see(Yii::$app->formatter->asCurrency(7.75), 'td'); // IVA
$I->see(Yii::$app->formatter->asCurrency(44.65), 'td'); // Total

// Pago
$I->click('Aceptar y Pagar');
$I->waitForText('Alta Pago', 20);
$I->selectOption("PaymentItem[payment_method_id]", 'Contado');
$I->fillField('PaymentItem[amount]', '44.65');
$I->click('Agregar');
$I->wait(1); // only for selenium
$I->click('Guardar');
$I->wait(1); // only for selenium

$I->waitForText('Cerrado'); // only for selenium
$I->dontSee('Completo');
$I->dontSee('Error');
$I->dontSee('error');

// Verifico número generado
$ein = $I->grabTextFrom(".//*[@id='ein-div']");
assert($ein != '');

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->see('Factura A');
