<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Product CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Productos", "Productos");

$I->makeScreenshot('sale_product_index');

$I->click('Alta Producto');
$I->wait(2);

$I->makeScreenshot('sale_product_create');

$I->click("Generar nuevo código");
$I->wait(1);

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Product[name]', 'Anana');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_product_view');

$I->see('Anana');
$I->click("Actualizar");

$I->makeScreenshot('sale_product_update');

$I->fillField('Product[name]', 'Anana*');
$I->click('Actualizar');
$I->wait(1); // only for selenium
$I->see('Anana*');

$I->see('Eliminar');
$I->click("Eliminar");
$I->acceptPopup();
$I->dontSee('Anana*');
