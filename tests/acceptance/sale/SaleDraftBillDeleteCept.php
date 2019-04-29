<?php

use tests\_pages\SalePage;
$I = new WebGuy($scenario);
$I->wantTo('ensure that delete draft bill works');

 $I->loginAsUser();

$I->clickMainMenu('Facturación', 'Factura A');

$url = $I->grabFromCurrentUrl();
$id = split('id=', $url)[1];

if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('Bill[company_id]', 'ACME');
}
$I->wait(1); // only for selenium
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
$I->wait(1); // only for selenium
$I->click('Agregar');
$I->wait(1); // only for selenium

// Guardo borrador
$I->click('Guardar borrador');
$I->wait(1); // only for selenium

// Verifico Historial

$I->click("(//a[@title = 'Eliminar'])[1]");
$I->acceptPopup();

$I->dontSee('El Comprobante no puede ser borrado');
