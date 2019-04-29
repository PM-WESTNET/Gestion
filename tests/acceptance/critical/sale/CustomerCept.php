<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer CRUD works');

 $I->loginAsSuperadmin();

$random = rand(111, 999);
$name = "Cliente $random";

$I->clickMainMenu("Clientes", "Clientes");

$I->makeScreenshot('sale_customer_index');

$I->click("//a[contains(@class, 'btn') and contains(text(), 'Alta Cliente')]");
$I->wait(1);

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
$I->waitForText("Nuevo, $name");

$I->click("Nuevo, $name");
$I->wait(1);

$I->see($name, 'h1');
$I->see('Nuevo');
$I->see('Consumidor Final');
$I->see('DNI');
$I->see('12345678');
$I->see('cliente@fake.com');
$I->see('1234567890');
$I->see('San Martín 1329, entre Buenos Aires y Lavalle, Bº Centro, Mendoza, M-J1 C-13 T-K2 P-32 D-O3');

$I->click('Actualizar');

$I->fillField('Customer[name]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

$I->makeScreenshot('sale_customer_update');

$I->see($name . ' *', 'h1');

$I->clickMainMenu("Clientes", "Clientes");

$I->selectOptionForSelect2('CustomerSearch[customer_id]', $random);

$I->waitForText($name . ' *');

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->reloadPage();
$I->wait(1);

$I->selectOptionForSelect2('CustomerSearch[customer_id]', $random);
$I->wait(1);

$I->dontSee($random);
