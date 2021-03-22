<?php

$random = rand(111, 999);
$name = "Zona $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Zone CRUD works');

$I->loginAsUser();

$I->clickMainMenu("Clientes", "Zonas");
$I->makeScreenshot('zone_zone_index');

$I->click('Alta Zona');
$I->makeScreenshot('zone_zone_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Tipo no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

// Verifico comportamiento correcto

$I->fillField('Zone[name]', $name . ' Madre');
$I->selectOption('Zone[type]', 'Localidad/Distrito');
$I->click('Alta');
$I->waitForText($name . ' Madre');

$I->makeScreenshot('zone_zone_view');

$I->see($name . ' Madre', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('zone_zone_update');

$I->fillField('Zone[name]', $name . ' Madre *');
$I->click('Actualizar');
$I->waitForText($name . ' Madre *');

$I->see($name . ' Madre *', 'h1');

// Verifico creación de hijos

$I->clickMainMenu("Clientes", "Zonas");
$I->click('Alta Zona');
$I->fillField('Zone[name]', $name . ' Hijo');
$I->selectOption('Zone[type]', 'Zona/Barrio');
$I->selectOptionForSelect2('Zone[parent_id]', $name . ' Madre *');
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Clientes", "Zonas");
$I->click('Alta Zona');
$I->fillField('Zone[name]', $name . ' Hija');
$I->selectOption('Zone[type]', 'Zona/Barrio');
$I->selectOptionForSelect2('Zone[parent_id]', $name . ' Madre *');
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Clientes", "Zonas");
$I->wait(1);

$I->see($name . ' Madre *');
$I->see($name . ' Hijo');
$I->see($name . ' Hija');

// Elimino hijos y madre

$I->fillField("//input[@name='ZoneSearch[name]']", $name);
$I->pressKey("//input[@name='ZoneSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->fillField("//input[@name='ZoneSearch[name]']", $name);
$I->pressKey("//input[@name='ZoneSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->fillField("//input[@name='ZoneSearch[name]']", $name);
$I->pressKey("//input[@name='ZoneSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1);
$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->dontSee($name);
