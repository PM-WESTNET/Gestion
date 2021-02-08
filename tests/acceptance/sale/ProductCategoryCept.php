<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Category CRUD works');

 $I->loginAsSuperadmin();

$random = rand(111, 999);
$name = "Category $random";
$edit_name = $name . ' *';

$I->clickMainMenu("Productos","Categorías");

$I->makeScreenshot('sale_product_category_index');

$I->click('Alta Categoría');

$I->makeScreenshot('sale_product_category_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Category[name]', $name);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_product_category_view');

$I->see($name, 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_product_category_update');

$I->fillField('Category[name]', $edit_name);
$I->click('Actualizar');
$I->wait(1);

$I->see($edit_name, 'h1');

// Verifico creación de hijos

$I->clickMainMenu("Productos","Categorías");
$I->click('Alta Categoría');
$I->fillField('Category[name]', $name . ' Hijo');
$I->selectOption('Category[parent_id]', $edit_name);
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Productos","Categorías");
$I->click('Alta Categoría');
$I->fillField('Category[name]', $name . ' Hija');
$I->selectOption('Category[parent_id]', $edit_name);
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Productos","Categorías");
$I->wait(1);

$I->see($edit_name);
$I->see($name . ' Hijo');
$I->see($name . ' Hija');

// Verifico que sean visibles en planes

$I->clickMainMenu("Productos","Productos");
$I->click('Acciones');
$I->click("Actualizar");
$I->wait(1);

$I->see($edit_name);
$I->see($name . ' Hijo');
$I->see($name . ' Hija');

// Elimino hijos y madre

$I->clickMainMenu("Productos","Categorías");
$I->wait(1);

$I->fillField('CategorySearch[name]', $random);
$I->pressKey("//input[@name='CategorySearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->fillField('CategorySearch[name]', $random);
$I->pressKey("//input[@name='CategorySearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->fillField('CategorySearch[name]', $random);
$I->pressKey("//input[@name='CategorySearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->dontSee($edit_name);
$I->dontSee($name . ' Hijo');
$I->dontSee($name . ' Hija');
