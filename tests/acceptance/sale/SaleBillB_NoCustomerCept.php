<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that sale works');

 $I->loginAsUser();

$I->amGoingTo('try to pay a invoice with no client');
$I->clickMainMenu('Facturación', 'Factura B');

$I->selectOption('Bill[company_id]', 'Metro');

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
$I->see('$7,74', 'td'); // Total

// Pago
$I->click('Pagar');
$I->wait(1); // only for selenium
$I->see('Alta Pago', 'h1');
$I->selectOption(".//*[@id='Payment_method_id1']", '1'); // Selecciono pago contado
$I->click('Alta');
$I->wait(1); // only for selenium

// Verifico mensaje de éxito
$I->dontSee('error');
$I->see('Venta');
$I->see('Pagado');

// Verifico número generado
$I->expect('number to be generated automatically');
$number = $I->grabTextFrom('html/body/div[1]/div/div[3]/div[3]/div[2]/div[2]/span[2]');
assert(isset($number) && $number != '');