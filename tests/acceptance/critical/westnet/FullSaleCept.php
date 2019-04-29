<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer creation, Contract creation and Contract Activation work');

 $I->loginAsSuperadmin();

$random = rand(111, 999);
$name = "Cliente $random";

$I->clickMainMenu("Clientes", "Clientes");

$I->makeScreenshot('sale_customer_index');

$I->click("//a[contains(@class, 'btn') and contains(text(), 'Alta Cliente')]");
$I->wait(1);

$I->makeScreenshot('sale_customer_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Apellido no puede estar vacío.');

// Verifico comportamiento correcto

$I->selectOption('Customer[parent_company_id]', 'Corporación');
$I->wait(1);
$I->selectOption('Customer[company_id]', 'ACME');
$I->fillField('Customer[name]', $name);
$I->fillField('Customer[lastname]', 'Nuevo');
$I->selectOption('Customer[tax_condition_id]', 'Consumidor Final');
$I->selectOption('Customer[document_type_id]', 'DNI');
$I->fillField('Customer[document_number]', '12345678');
$I->fillField('Customer[email]', 'cliente@fake.com');
$I->fillField('Customer[phone]', '1234567890');
$I->selectOption('Customer[publicity_shape]', 'Banner');
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
$I->fillField('Address[geocode]', '-32.89888,-68.81946');

$I->click('Alta');
$I->wait(1);
$I->waitForText('Nuevo Contrato');

$I->selectOption('Contract[vendor_id]', 'V, Vendor');
$I->selectOption('ContractDetail[product_id]', 'Silver - $726');
$I->selectOption('contractDetailIns[product_id]', 'Instalación');
$I->selectOptionForSelect2('contractDetailIns[funding_plan_id]', '1 cuota');
$I->selectOption('Contract[instalation_schedule]', 'Por la Tarde');
$I->checkOption('#same_address');

$I->click('Alta y Finalizar');
$I->wait(1);

$I->waitForText('Detalles de Contrato');

$I->click('Activar Contrato');
$I->wait(1);

$I->selectOptionForSelect2('Connection[node_id]', 'Origen');
$I->wait(1);
$I->click('Activar');
$I->acceptPopup();
$I->wait(1);

$I->waitForText('Activo');
        
//$I->click('Activar');
//$I->acceptPopup();
//$I->wait(1);
//
//$I->waitForText('Habilitada');

// Limpio db luego de test

$today = date('Y-m-d');

$sql_contract = "SELECT `contract_id` FROM `contract` where `date` = $today";
$sql_contract_detail = "SELECT `contract_detail_id` FROM `contract_detail` where `contract_id` in ($sql_contract)";

TestDbHelper::execute("DELETE FROM `connection` where `contract_id` in ($sql_contract)");
TestDbHelper::execute("DELETE FROM `product_to_invoice` where `contract_detail_id` in ($sql_contract_detail)");
TestDbHelper::execute("DELETE FROM `contract_detail` where `contract_detail_id` in ($sql_contract)");
TestDbHelper::execute("DELETE FROM `contract` where `date` = $today");
