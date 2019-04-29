<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Product Search works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Productos", "Productos");

// Verifico valores iniciales

$I->see('Pera', 'td');
$I->see('Manzana', 'td');

// Pruebo filtro de columnas
$I->fillColumnSearchField('ProductSearch[name]', 'pe');
$I->see('Pera', 'td');
$I->dontSee('Manzana', 'td');

$I->clickMainMenu("Productos", "Productos");

$I->fillColumnSearchField('ProductSearch[code]', '69');
$I->dontSee('Pera', 'td');
$I->see('Manzana', 'td');

$I->clickMainMenu("Productos", "Productos");

$I->fillColumnSearchField('ProductSearch[description]', 'Py');
$I->see('Pera', 'td');
$I->dontSee('Manzana', 'td');

// Pruebo filtro especializado

$I->clickMainMenu("Productos", "Productos");

$I->fillField('ProductSearch[search_text]', 'Ma');
$I->pressKey(".//*[@id='search_text']", WebDriverKeys::ENTER);
$I->wait(1);

$I->dontSee('Pera', 'td');
$I->see('Manzana', 'td');
