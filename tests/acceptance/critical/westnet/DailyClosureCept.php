<?php

$payments = [
    '9999123' => rand(1111, 9999) / 10,
    '9999234' => rand(1111, 9999) / 10,
    '9999345' => rand(1111, 9999) / 10,
];

$total = 0;
foreach ($payments as $code => $payment) {
    $total += $payment;
}

$I = new WebGuy($scenario);
$I->wantTo('ensure that Daily Closure works');

$dbhelper = new TestDbHelper();
$dbhelper->initializeDb($I);

$I->login('diego', 'diego');

// Aseguro caja cerrada

try {
    $I->dontSee('Abra la caja para poder procesar pagos');

    $I->click('Cierres diarios');
    $I->click('Ejecutar cierre diario');

    $I->wait(1);
    $I->click(".//*[@id='execute-button']");

    $I->see('Cierre diario ejecutado con éxito!');

    $I->click("Home");
} catch (Exception $ex) {
    
}

$I->click('Abrir caja');

// Registro pagos

$previous_payment = null;

foreach ($payments as $code => $payment) {
    $I->fillField('Payout[customer_number]', $code);
    $I->pressKey(".//*[@id='payout-customer_number']", WebDriverKeys::ENTER);
    $I->waitForElementNotVisible('#overlay', 10); // secs
    $I->wait(2);

    if (isset($previous_payment)) {
        $I->see(Yii::$app->formatter->asCurrency($previous_payment));
    }

    $I->fillField('Payout[amount]', $payment);
    $I->click(".//*[@id='btn-submit']");
    $I->wait(1);
    $I->acceptPopup();
    $I->wait(1);

    //$I->click("Cerrar (ESC)");
    //$I->wait(1);

    $previous_payment = $payment;
}

// Creo cierre diario

$I->amOnPage('index-test.php?r=westnet%2Fecopagos%2Ffrontend%2Fsite%2Findex');
$I->click('Cierres diarios');
$I->click('Ejecutar cierre diario');

$I->see('Resumen de cierre diario');

$I->see(count($payments));
$I->see(Yii::$app->formatter->asCurrency($total));
$I->dontSee('No se encontraron pagos para esta caja.');

$I->wait(1);
$I->click(".//*[@id='execute-button']");

$I->see('Cierre diario ejecutado con éxito!');

$I->click("Cerrar (ESC)");
$I->wait(1);

$I->click('Cierres diarios');
$I->click('Ver cierres diarios');

$I->see($total);

// Clean up database
foreach ($payments as $payment) {
    TestDbHelper::execute("DELETE FROM `payment_item` where `payment_id` in (SELECT `payment_id` FROM `payment` where `balance` = $payment)");
    TestDbHelper::execute("DELETE FROM `payment` where `balance` = $payment");
    TestDbHelper::execute("DELETE FROM `payout` where `amount` = $payment", 'ecopago');
}

TestDbHelper::execute("DELETE FROM `daily_closure` where `total` = $total", 'ecopago');
