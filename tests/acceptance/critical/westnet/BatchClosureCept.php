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
$I->wantTo('ensure that Batch Closure works');

$dbhelper = new TestDbHelper();
$dbhelper->initializeDb($I);

$I->login('diego', 'diego');

$I->click('Abrir caja');
$I->wait(2);

// Registro pagos

foreach ($payments as $code => $payment) {
    $I->fillField('Payout[customer_number]', $code);
    $I->pressKey(".//*[@id='payout-customer_number']", WebDriverKeys::ENTER);
    $I->waitForElementNotVisible('#overlay', 10); // secs
    $I->wait(2);

    $I->fillField('Payout[amount]', $payment);
    $I->click(".//*[@id='btn-submit']");
    $I->acceptPopup();
    $I->wait(1);

    //$I->click("Cerrar (ESC)");
    //$I->wait(1);
}

// Creo cierre de lote

$I->amOnPage('index-test.php?r=westnet%2Fecopagos%2Ffrontend%2Fsite%2Findex');
$I->click('Cierres de lote');
$I->click('Ejecutar cierre de lote');

$I->see('Ejecutar cierre de lote');

// Confirmo autenticación

$I->fillField('Collector[number]', '321');
$I->fillField('Collector[password]', '321');
$I->pressKey(".//*[@id='collector-password']", WebDriverKeys::ENTER);

$I->waitForElementNotVisible('#overlay', 10); // secs
$I->wait(2);

$I->see('Carlos Renati');

$I->click("Ver detalles del cierre de lote");
$I->wait(1);

$I->see('Detalles del cierre de lote');
$I->see(count($payments));
$I->see(Yii::$app->formatter->asCurrency($total));

$I->waitForElementVisible(".//*[@id='batch-closure-submit']", 10); // secs
$I->click(".//*[@id='batch-closure-submit']");
$I->wait(1);

$I->click("Cerrar (ESC)");
$I->wait(1);

$I->see('Cierre de lote ejecutado con éxito!');
$I->see(count($payments));
$I->see(Yii::$app->formatter->asCurrency($total));

$I->click('Ver lista de pagos procesados');

foreach ($payments as $payment) {
    $I->see(Yii::$app->formatter->asCurrency($payment));
}

// Confirmo no duplicidad

$I->click('Cierres de lote');
$I->click('Ejecutar cierre de lote');

$I->fillField('Collector[number]', '321');
$I->fillField('Collector[password]', '321');
$I->pressKey(".//*[@id='collector-password']", WebDriverKeys::ENTER);

$I->waitForElementNotVisible('#overlay', 10); // secs
$I->waitForElementVisible(".//*[@id='form-batch-closure']/div[5]/a", 10); // secs
$I->wait(2);

$I->see('Carlos Renati');

$I->click("Ver detalles del cierre de lote");
$I->wait(1);

$I->see('No se encontraron pagos');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Salir');

$I->loginAsSuperadmin();

$I->clickMainMenu('Westnet', 'Cierres de lote');

$I->see(Yii::$app->formatter->asCurrency($total));

$I->click("(.//a[@title = 'Ver'])[1]");
$I->wait(1);

$I->see(Yii::$app->formatter->asCurrency($total));

$I->click("Rendir cierre de lote");
$I->wait(1);

$I->see(Yii::$app->formatter->asCurrency($total));

$I->click("Rendir cierre de lote");
$I->wait(1);

$difference = rand(111, 999) / 10;
$real_total = $total - $difference;

$I->selectOption('BatchClosure[money_box_account_id]', 'Godoy Cruz');
$I->fillField('BatchClosure[real_total]', $real_total);

$I->click("Rendir cierre de lote");
$I->wait(1);

$I->see('Cierre de lote rendido con éxito!');

$I->see(Yii::$app->formatter->asCurrency($total));
$I->see(Yii::$app->formatter->asCurrency($real_total));
$I->see(Yii::$app->formatter->asCurrency($difference));

// Clean up database
foreach ($payments as $payment) {
    TestDbHelper::execute("DELETE FROM `payment_item` where `payment_id` in (SELECT `payment_id` FROM `payment` where `balance` = $payment)");
    TestDbHelper::execute("DELETE FROM `payment` where `balance` = $payment");

    TestDbHelper::execute("DELETE FROM `batch_closure_has_payout` where `payout_id` in (SELECT `payout_id` FROM `payout` where `amount` = $payment)", 'ecopago');
    TestDbHelper::execute("DELETE FROM `payout` where `amount` = $payment", 'ecopago');
}

TestDbHelper::execute("DELETE FROM `batch_closure` where `total` = $total", 'ecopago');
