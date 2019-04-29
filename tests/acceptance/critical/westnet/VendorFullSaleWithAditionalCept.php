<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer creation, Contract creation and Contract Activation work');

 $I->loginAsVendor();

$random = rand(111, 999);
$name = "Cliente $random";

$I->clickMainMenu("Vendedores", "Alta Cliente");

$I->click("Nuevo Cliente");
$I->wait(1);

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Apellido no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Customer[name]', $name);
$I->fillField('Customer[lastname]', 'Nuevo');
$I->selectOption('Customer[tax_condition_id]', 'Consumidor Final');
$I->selectOption('Customer[document_type_id]', 'DNI');
$I->fillField('Customer[document_number]', '12345678');
$I->fillField('Customer[email]', 'cliente@fake.com');
$I->fillField('Customer[phone]', '1234567890');
if (Yii::$app->params['class_customer_required']) {
    $I->selectOption('Customer[customerClass]', 'Mantenimiento');
}
if (Yii::$app->params['category_customer_required']) {
    $I->selectOption('Customer[customerCategory]', 'Empresa');
}
$I->click('Dirección');
$I->wait(1);
if (Yii::$app->getModule('zone')) {
    $I->selectOptionForSelect2('Address[zone_id]', 'Centro');
}
$I->fillField('Address[street]', 'San Martín');
$I->fillField('Address[number]', '1329');
$I->fillField('Address[between_street_1]', 'Buenos Aires');
$I->fillField('Address[between_street_2]', 'Lavalle');
$I->fillField('Address[block]', 'J1');
$I->fillField('Address[house]', '13');
$I->fillField('Address[tower]', 'K2');
$I->fillField('Address[floor]', '32');
$I->fillField('Address[department]', 'O3');

$I->click('Alta');
$I->wait(1);
$I->waitForText('Nuevo Contrato');

$I->selectOption('contractDetailIns[product_id]', 'Instalación');
$I->selectOptionForSelect2('contractDetailIns[funding_plan_id]', '1 cuota');
$I->selectOption('ContractDetail[product_id]', 'Silver - $726');
$I->selectOption('Contract[instalation_schedule]', 'Por la Tarde');
$I->checkOption('#same_address');

$I->click('Alta y agregar adicionales');
$I->wait(1);
$I->waitForText('Actualizar Contrato');

$I->selectOptionForSelect2('ContractDetail[product_id]', 'Router');
$I->selectOptionForSelect2('ContractDetail[funding_plan_id]', '1 cuota');

$I->click('Agregar');
$I->wait(1);

$I->see('Router', 'td');

$I->click('Actualizar');
$I->wait(1);

$I->waitForText('Detalles de Contrato');

$I->see("V, Vendor", ".//*[@id='w0']/tbody/tr[9]/td");
$I->see("V, Vendor", ".//*[@id='w1']/table/tbody/tr[1]/td[11]");
$I->see("V, Vendor", ".//*[@id='w1']/table/tbody/tr[2]/td[11]");

// Limpio db luego de test

$today = date('Y-m-d');

$sql_contract = "SELECT `contract_id` FROM `contract` where `date` = $today";
$sql_contract_detail = "SELECT `contract_detail_id` FROM `contract_detail` where `contract_id` in ($sql_contract)";

TestDbHelper::execute("DELETE FROM `connection` where `contract_id` in ($sql_contract)");
TestDbHelper::execute("DELETE FROM `product_to_invoice` where `contract_detail_id` in ($sql_contract_detail)");
TestDbHelper::execute("DELETE FROM `contract_detail` where `contract_detail_id` in ($sql_contract)");
TestDbHelper::execute("DELETE FROM `contract` where `date` = $today");
