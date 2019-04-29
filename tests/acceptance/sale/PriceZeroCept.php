<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that can bill product with prize in zero');

 $I->loginAsSuperadmin();

do {
    $kiwi_quantity = rand(10, 99);
} while ($kiwi_quantity % 10 == 0);
$kiwi_quantity = $kiwi_quantity / 10;

// Pongo el precio de un producto con precio cero

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
$I->fillField('#search_text', 'kiw');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->click('Agregar');
$I->wait(1); // only for selenium
// Defino valores de compra
$I->fillField("//input[@id='input-qty0']", $kiwi_quantity);
$I->pressKey("//input[@id='input-qty0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

// Verifico cálculo correcto
$I->see(Yii::$app->formatter->asCurrency(0), 'td'); // Subtotal
$I->click('Cerrar');
$I->wait(1); // only for selenium
// Verifico mensaje de éxito
$I->dontSee('error');
$I->see('Cerrado');

// Inicio Factura A

$I->clickMainMenu("Comprobantes", "Presupuesto");
$I->click("//a[@title='Ver']");
$I->click("//a[contains(text(), 'Factura A') and contains(@class, 'btn')]");
$I->wait(1); // only for selenium

$I->seeInField('BillDetail[qty]', $kiwi_quantity);
$I->seeInField('BillDetail[unit_net_price]', Yii::$app->formatter->asDecimal(0));
$I->see(Yii::$app->formatter->asCurrency(0), 'td'); // Subtotal

$I->click('Cerrar');
$I->wait(1); // only for selenium

$I->dontSee('error');
$I->see('Cerrado');

// Verifico Historial

$I->click('Historial');
$I->wait(1); // only for selenium

$I->see('Presupuesto');
$I->see('Factura A');
