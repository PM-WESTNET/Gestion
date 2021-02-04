<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer Embed CRUD works');

 $I->loginAsSuperadmin();

$random = rand(111, 999);
$name = "Cliente $random";

$I->clickMainMenu('Facturación', 'Factura A');
$I->wait(1); // only for selenium

$I->click('Alta Cliente');
$I->wait(1); // only for selenium
$I->switchToIFrame("customer-iframe");

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico mensaje de campos requeridos luego de post

$I->fillField('Customer[name]', $name);
$I->fillField('Customer[lastname]', $name);
$I->click('Alta');
$I->wait(1);

$I->see('El tipo de cliente "Consumidor Final" requiere la carga de "DNI".');
$I->see('Nro de Doc. no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Customer[name]', $name);
$I->fillField('Customer[lastname]', 'Nuevo');
$I->selectOption('Customer[tax_condition_id]', 'Consumidor Final');
$I->selectOption('Customer[document_type_id]', 'DNI');
$I->fillField('Customer[document_number]', '12345678');

if (Yii::$app->getModule('accounting')) {
    $I->selectOptionForSelect2('Customer[account_id]', 'Caja Pesos');
}
$I->click('Alta');
$I->wait(1);

$I->switchToIFrame();
$I->wait(1);

$I->reloadPage();
$I->seeElementInDOM("//input[contains(@value, '$name')]");

// Eliminar está deshabilitado para todas las empresas, se elimina manualmente
TestDbHelper::execute("DELETE FROM `customer_class_has_customer` where `customer_id` in (SELECT `customer_id` FROM `customer` where `name` like '%$random%')");
TestDbHelper::execute("DELETE FROM `customer_category_has_customer` where `customer_id` in (SELECT `customer_id` FROM `customer` where `name` like '%$random%')");
TestDbHelper::execute("DELETE FROM `bill` where `customer_id` in (SELECT `customer_id` FROM `customer` where `name` like '%$random%')");
TestDbHelper::execute("DELETE FROM `product_to_invoice` where `contract_detail_id` in ("
        . "select `contract_detail_id` FROM `contract_detail` where `contract_id` in ("
        . "select `contract_id` FROM `contract` where `description` like '%$random%'"
        . "))");
TestDbHelper::execute("DELETE FROM `contract_detail` where `contract_id` in (SELECT `contract_id` FROM `contract` where `customer_id` in (SELECT `customer_id` FROM `customer` where `name` like '%$random%'))");
TestDbHelper::execute("DELETE FROM `contract` where `customer_id` in (SELECT `customer_id` FROM `customer` where `name` like '%$random%')");
TestDbHelper::execute("DELETE FROM `customer` where `name` like '%$random%'");
