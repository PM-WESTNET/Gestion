<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Payment Method CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu('Comprobantes', 'Configuración', 'Medios de pago');

$I->makeScreenshot('checkout_payment_method_index');

$I->click('Alta Medio de pago');

$I->makeScreenshot('checkout_payment_method_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico mensaje de campos mal ingresados
// Verifico comportamiento correcto

$I->fillField('PaymentMethod[name]', 'Método A');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('checkout_payment_method_view');

$I->see('Método A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('checkout_payment_method_update');

$I->fillField('PaymentMethod[name]', 'Método A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Método A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Método A *');
