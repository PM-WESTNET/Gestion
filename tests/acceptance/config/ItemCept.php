<?php


$random = rand(100, 999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Config/Item CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Aplicación", "Ítems de configuración");

$I->makeScreenshot('config_item_index');

$I->click('Alta Ítem');

$I->makeScreenshot('config_item_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Atributo no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Item[attr]', 'Item ' . $random);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('config_item_view');

$I->see('Item ' . $random, 'h1');
$I->click('Actualizar');

$I->makeScreenshot('config_item_update');

$I->fillField('Item[attr]', 'Item *' . $random);
$I->click('Actualizar');
$I->wait(1);

$I->see('Item *' . $random, 'h1');

// Verifico attr duplicado

$I->clickMainMenu("Aplicación", "Ítems de configuración");

$I->click('Alta Ítem');

$I->fillField('Item[attr]', 'Item *' . $random);
$I->click('Alta');
$I->wait(1);
$I->see("Atributo \"Item *$random\" ya ha sido utilizado.");

// Elimino

$I->clickMainMenu("Aplicación", "Ítems de configuración");

$I->fillField('ItemSearch[attr]', 'Item');
$I->pressKey("//input[@name='ItemSearch[attr]']", WebDriverKeys::ENTER);
$I->wait(1);

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->dontSee('Item *' . $random);