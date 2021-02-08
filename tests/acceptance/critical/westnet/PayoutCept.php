<?php

$name = 'Ecopago ' . rand(1111, 9999);
$amount = rand(100, 9999) / 10;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ecopago Payout works');

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

// verifico mensaje uso incorrecto

$I->fillField('Payout[customer_number]', '9876543210');
$I->pressKey(".//*[@id='payout-customer_number']", WebDriverKeys::ENTER);
$I->wait(1);

$I->see('No se pudo encontrar información de cliente con el número ingresado');

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

$I->amOnPage('index-test.php?r=westnet%2Fecopagos%2Ffrontend%2Fsite%2Findex');

$I->click('Opciones del cobrador');
$I->click('Cambiar cobrador');

$I->loginAsSuperadmin();
$I->clickMainMenu("Clientes", "Clientes");

$I->selectOptionForSelect2('CustomerSearch[customer_id]', 'Juan');
$I->click('Filtrar');
$I->wait(1);

$I->click(".//a[@title='Ver']");
$I->wait(1);

$I->click("Cuenta corriente");
$I->wait(1);

$I->see(Yii::$app->formatter->asCurrency($amount));

$I->click(".//a[@title='Ver']");

$I->clickMainMenu("Westnet", "Pagos");

$I->see(Yii::$app->formatter->asCurrency($amount));

$I->fillColumnSearchField('PayoutSearch[amount]', $amount);

$I->see(Yii::$app->formatter->asCurrency($amount));

$I->fillColumnSearchField('PayoutSearch[date]', date('d-m-Y'));

$I->see(Yii::$app->formatter->asCurrency($amount));

// Eliminar está deshabilitado para todas las empresas, se elimina manualmente
TestDbHelper::execute("DELETE FROM `payment_item` where `payment_id` in (SELECT `payment_id` FROM `payment` where `balance` = $amount)");
TestDbHelper::execute("DELETE FROM `payment` where `balance` = $amount");
TestDbHelper::execute("DELETE FROM `payout` where `amount` = $amount", 'ecopago');

$I->wait(1);

$I->reloadPage();
$I->dontSee('Contado');
$I->dontsee('Pago Ecopago');

$I->click("//span[contains(@class,'user')]/..");
$I->click('Logout');

$I->login('diego', 'diego');

$I->click("Pagos");
$I->click("Ver pagos registrados");

$I->dontSee($amount, 'td');
