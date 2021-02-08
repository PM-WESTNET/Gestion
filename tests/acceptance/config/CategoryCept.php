<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Config/Category CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Aplicación", 'Categorías de configuración');

$I->makeScreenshot('config_category_index');

$I->click('Alta Categoría');

$I->makeScreenshot('config_category_create');

// Verifico mensaje de campos requeridos
$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Category[name]', 'Categoría A');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('config_category_view');

$I->see('Categoría A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('config_category_update');

$I->fillField('Category[name]', 'Categoría A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Categoría A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Categoría A *');