<?php

$name = 'Ecopago ' . rand(1111, 9999);
$amount = rand(100, 9999) / 10;

$I = new WebGuy($scenario);
$I->wantTo('ensure that ecopago double payout are forbidden');

$dbhelper = new TestDbHelper();
$dbhelper->initializeDb($I);

$I->login('diego', 'diego');

// Aseguro caja abierta

try {
    $I->see("Caja está abierta");
    $I->click('Cierres diarios');
    $I->click('Ejecutar cierre diario');
    $I->see('Está cerrando la caja');
    $I->click('#execute-button');
} catch (Exception $ex) {

}

try {
    $I->see('Abra la caja para poder procesar pagos');
    $I->click('Abrir caja');
} catch (Exception $ex) {
    $I->click('Pagos');
    $I->click('Registrar pago');
}

// Verifico comportamiento correcto

$I->fillField('Payout[customer_number]', '9999123');
$I->pressKey(".//*[@id='payout-customer_number']", WebDriverKeys::ENTER);

$I->waitForElementNotVisible('#overlay', 10); // secs
$I->wait(2);

$I->see('Información sobre Juan Garcia');

$I->fillField('Payout[amount]', $amount);
$I->click(".//*[@id='btn-submit']");
$I->wait(1);
$I->acceptPopup();
$I->wait(1);

$I->see(Yii::$app->formatter->asCurrency($amount));

// Verifico que mensaje de doble pago

$I->fillField('Payout[customer_number]', '9999123');
$I->pressKey(".//*[@id='payout-customer_number']", WebDriverKeys::ENTER);
$I->wait(1);

$I->waitForText('No se permiten dos pagos del mismo cliente en la misma caja');

// Eliminar está deshabilitado para todas las empresas, se elimina manualmente
TestDbHelper::execute("DELETE FROM `payment_item` where `payment_id` in (SELECT `payment_id` FROM `payment` where `balance` = $amount)");
TestDbHelper::execute("DELETE FROM `payment` where `balance` = $amount");
TestDbHelper::execute("DELETE FROM `payout` where `amount` = $amount", 'ecopago');

$I->wait(1);

$I->reloadPage(); // expected #404 since payment was deleted in DB
$I->dontSee('Contado');
$I->dontsee('Pago Ecopago');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Logout');

$I->login('diego', 'diego');

$I->click("Pagos");
$I->click("Ver pagos registrados");

$I->dontSee($amount, 'td');
