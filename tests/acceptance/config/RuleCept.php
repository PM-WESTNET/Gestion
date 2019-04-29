<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Zone CRUD works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Aplicación", "Ítems de configuración");

$I->fillField('ItemSearch[attr]', 'payment');
$I->pressKey("//input[@name='ItemSearch[attr]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Ver']");

$I->click('Agregar validador');

$I->makeScreenshot('config_rule_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Validador no puede estar vacío.');

// Verifico comportamiento correcto

$I->selectOption('Rule[validator]', 'boolean');
$I->wait(1);
$I->fillField('Rule[message]', 'Validador');
$I->click('Alta');
$I->wait(1);

$I->see('Validador', 'td');

$I->click("//a[@title='Actualizar']");
$I->wait(1);

$I->makeScreenshot('config_rule_update');

$I->fillField('Rule[message]', 'Validador *');

$I->click('Actualizar');
$I->wait(1);

$I->see('Validador *', 'td');

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->dontSee('Validador *', 'td');
