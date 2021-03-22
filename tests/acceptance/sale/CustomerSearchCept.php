<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer Search works');

 $I->loginAsAdmin();

$I->clickMainMenu("Clientes", "Clientes");

// Pruebo filtro de columnas

$I->fillColumnSearchField('CustomerSearch[customer_id]', '1');
$I->see('Juan', 'td');
$I->dontSee('José', 'td');

$I->clickMainMenu("Clientes", "Clientes");

$I->fillColumnSearchField('CustomerSearch[name]', 'se');
$I->dontSee('Juan', 'td');
$I->see('José', 'td');

$I->clickMainMenu("Clientes", "Clientes");

$I->fillColumnSearchField('CustomerSearch[lastname]', 'ga');
$I->see('Juan', 'td');
$I->dontSee('José', 'td');

$I->clickMainMenu("Clientes", "Clientes");

$I->fillColumnSearchField('CustomerSearch[document_number]', '78');
$I->dontSee('Juan', 'td');
$I->see('José', 'td');

$I->clickMainMenu("Clientes", "Clientes");

$I->fillColumnSearchField('CustomerSearch[email]', 'ia.com');
$I->see('Juan', 'td');
$I->dontSee('José', 'td');

$I->clickMainMenu("Clientes", "Clientes");

$I->fillColumnSearchField('CustomerSearch[phone]', '(11)');
$I->dontSee('Juan', 'td');
$I->see('José', 'td');

// Pruebo filtro especializado

$I->clickMainMenu("Clientes", "Clientes");
$I->click('Filtros');
$I->wait(1);
$I->fillField('CustomerSearch[search_text]', 'Ju');
$I->pressKey(".//*[@id='search_text']", WebDriverKeys::ENTER);
$I->wait(1);

$I->see('Juan', 'td');
$I->dontSee('José', 'td');
