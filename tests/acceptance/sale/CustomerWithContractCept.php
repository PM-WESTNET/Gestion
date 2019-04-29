<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer CRUD works');

 $I->loginAsAdmin();

$random = rand(111, 999);
$name = "Cliente $random";

$I->clickMainMenu("Clientes", "Clientes");

$I->click("//a[contains(@class, 'btn') and contains(text(), 'Alta Cliente')]");
$I->wait(1);

// Verifico comportamiento correcto

$I->fillField('Customer[name]', $name);
$I->fillField('Customer[lastname]', 'Nuevo');
$I->selectOption('Customer[tax_condition_id]', 'Consumidor Final');
$I->selectOption('Customer[document_type_id]', 'DNI');
$I->fillField('Customer[document_number]', '12345678');
$I->fillField('Customer[email]', 'cliente@fake.com');
$I->fillField('Customer[phone]', '1234567890');
$I->click('Dirección');
$I->wait(1);
$I->fillField('Address[street]', 'San Martín');
$I->fillField('Address[number]', '1329');
$I->fillField('Address[between_street_1]', 'Buenos Aires');
$I->fillField('Address[between_street_2]', 'Lavalle');
$I->fillField('Address[block]', 'J1');
$I->fillField('Address[house]', '13');
$I->fillField('Address[tower]', 'K2');
$I->fillField('Address[floor]', '32');
$I->fillField('Address[department]', 'O3');
$I->fillField('Address[geocode]', '-32.88821, -68.83807');
if (Yii::$app->getModule('accounting')) {
    $I->selectOptionForSelect2('Customer[account_id]', 'Caja Pesos');
}
if (Yii::$app->getModule('zone')) {
    $I->selectOptionForSelect2('Address[zone_id]', 'Centro');
}
$I->click('Alta');
$I->wait(1);

// Lleno el nuevo contrato

$I->see('Nuevo contrato');

$I->checkOption(".//*[@id='same_address']");

$I->click('Alta');
$I->wait(1);
$I->see('Plan no puede estar vacío.');

$I->selectOption('ContractDetail[product_id]', 'Bronze');
$I->click('Alta');
$I->wait(1);

$I->click('Actualizar');
$I->wait(1);

$I->see($name);
$I->see('San Martín 1329, M-J1 C-13 T-K2 P-32 D-O3, Centro');
$I->see('Borrador');
$I->see('Tiempo indeterminado');
$I->see('Bronze');

// Verifico edición de contrato

$I->click('Actualizar');
$I->wait(1);

$I->selectOption('ContractDetail[product_id]', 'Silver');

$I->click("Actualizar");
$I->wait(1);

$I->see('Silver');

$I->click("Historial");
$I->wait(1);

$I->see('Silver');
//$I->see('Bronze');

$I->click("Volver");
$I->wait(1);

$I->click("Activar Contrato");
$I->wait(1);

$I->click("Activar");
$I->acceptPopup();
$I->wait(1);

$I->see('Nodo no puede estar vacío.');

$I->fillField('Contract[from_date]', date('dd-MM-yyyy'));
$I->selectOptionForSelect2('Connection[node_id]', 'Origen');
$I->selectOption('Connection[use_second_ip]', 'Si');
$I->selectOption('Connection[has_public_ip]', 'Si');
$I->fillField('Connection[ip4_public]', '8.8.4.4');

$I->click("Activar");
$I->acceptPopup();
$I->wait(1);

$I->see('Activo');

// Eliminar está deshabilitado para todas las empresas, se elimina manualmente
$customerQuery = "select `customer_id` FROM `customer` where `name` like '%$random%'";
$contractQuery = "select `contract_id` FROM `contract` where `customer_id` in ($customerQuery)";

TestDbHelper::execute("DELETE FROM `product_to_invoice` where `contract_detail_id` in ("
        . "select `contract_detail_id` FROM `contract_detail` where `contract_id` in ($contractQuery))");
TestDbHelper::execute("DELETE FROM `connection` where `contract_id` in ($contractQuery)");
TestDbHelper::execute("DELETE FROM `contract_detail` where `contract_id` in ($contractQuery)");
TestDbHelper::execute("DELETE FROM `contract` where `customer_id` in ($customerQuery)");
TestDbHelper::execute("DELETE FROM `customer_class_has_customer` where `customer_id` in ($customerQuery)");
TestDbHelper::execute("DELETE FROM `customer_category_has_customer` where `customer_id` in ($customerQuery)");
TestDbHelper::execute("DELETE FROM `customer` where `name` like '%$random%'");