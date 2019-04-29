<?php

$random = rand(111, 999);
$name = "Discount $random";
$value = $random / 10;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Discount CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Clientes", 'Descuentos');

$I->makeScreenshot('sale_discount_index');

$I->click('Alta Descuento');

$I->makeScreenshot('sale_discount_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Discount[name]', $name);
$I->fillField('Discount[from_date]', date('01-m-Y'));
$I->fillField('Discount[to_date]', date('29-m-Y'));
$I->selectOption('Discount[apply_to]', 'Producto');
$I->selectOption('Discount[value_from]', 'Producto');
$I->selectOptionForSelect2('Discount[product_id]', 'Bronze');
$I->selectOption('Discount[status]', 'Disponible');
$I->selectOption('Discount[type]', 'Fijo');
$I->fillField('Discount[value]', $value);
$I->fillField('Discount[periods]', 3);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_discount_view');

$I->see($name, 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_discount_update');

$I->fillField('Discount[name]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' *', 'h1');

// Verifico disponibilidad en contratos

$I->clickMainMenu("Clientes", "Clientes");
$I->click('Filtros');
$I->wait(1);
$I->selectOptionForSelect2('CustomerSearch[customer_id]', 'Juan');
$I->click('Filtrar');
$I->wait(1);

$I->click("//a[@title='Ver']");
$I->wait(1);

$I->click('Nuevo Contrato');

$I->selectOption('ContractDetail[product_id]', 'Bronze');
$I->wait(1);
$I->selectOption('ContractDetail[discount_id]', $name . ' *');

$I->clickMainMenu("Clientes", 'Descuentos');

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name . ' *');
