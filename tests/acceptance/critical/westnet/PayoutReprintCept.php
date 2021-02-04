<?php

$name = 'Ecopago ' . rand(1111, 9999);
$amount = rand(100, 9999) / 10;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ecopago Payout works');

$dbhelper = new TestDbHelper();
$dbhelper->initializeDb($I);

$I->login('diego', 'diego');

// Aseguro caja abierta

$I->see('Abra la caja para poder procesar pagos');
$I->click('Abrir caja');

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

$I->click("Cerrar (ESC)");
$I->wait(1);

$I->click('#btn-observation-print');

$I->wait(100);
