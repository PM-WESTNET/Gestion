<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Plan Feature CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Productos","Características de Plan");

$I->makeScreenshot('sale_plan_feature_index');

$I->click('Alta Característica de Plan');

$I->makeScreenshot('sale_plan_feature_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('PlanFeature[name]', 'Disponibilidad');
$I->selectOption('PlanFeature[type]', 'Checkbox');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_plan_feature_view');

$I->see('Disponibilidad', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_plan_feature_update');

$I->fillField('PlanFeature[name]', 'Disponibilidad *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Disponibilidad *', 'h1');

// Verifico creación de hijos

$I->clickMainMenu("Productos","Características de Plan");
$I->click('Alta Característica de Plan');
$I->fillField('PlanFeature[name]', 'Ahora');
$I->selectOption('PlanFeature[parent_id]', 'Disponibilidad *');
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Productos","Características de Plan");
$I->click('Alta Característica de Plan');
$I->fillField('PlanFeature[name]', 'Después');
$I->selectOption('PlanFeature[parent_id]', 'Disponibilidad *');
$I->click('Alta');
$I->wait(1);

$I->clickMainMenu("Productos","Características de Plan");
$I->wait(1);

$I->see('Disponibilidad *');
$I->see('Ahora');
$I->see('Después');

// Verifico que sean visibles en planes

$I->clickMainMenu("Productos","Planes");
$I->click("//a[@title='Actualizar']");

$I->see('Disponibilidad *');
$I->see('Ahora');
$I->see('Después');

// Elimino hijos y madre

$I->clickMainMenu("Productos","Características de Plan");
$I->wait(1);

$I->fillField('PlanFeatureSearch[name]', 'ahora');
$I->pressKey(".//*[@name='PlanFeatureSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->fillField('PlanFeatureSearch[name]', 'despues');
$I->pressKey(".//*[@name='PlanFeatureSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();

$I->fillField('PlanFeatureSearch[name]', 'disponibilidad');
$I->pressKey(".//*[@name='PlanFeatureSearch[name]']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->click("//a[@title='Eliminar']");
$I->acceptPopup();
$I->wait(1);

$I->dontSee('Disponibilidad *');
$I->dontSee('Ahora');
$I->dontSee('Después');