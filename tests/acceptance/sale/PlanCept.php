<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Plan CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Productos","Planes");

$I->makeScreenshot('sale_plan_index');

$I->click('Alta Plan');

$I->makeScreenshot('sale_plan_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Plan[name]', 'Plan A');
$I->checkOption("//input[@name='Plan[_planfeature][1][]'][@value=2]");
$I->checkOption("//input[@name='Plan[_planfeature][4]'][@value=5]");
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_plan_view');

$I->see('Plan A', 'h1');
$I->see('Activación');
$I->see('Inmediata');
$I->see('Velocidad');
$I->see('Rápida');
$I->dontSee('Lenta');
$I->click('Actualizar');

$I->makeScreenshot('sale_plan_update');

$I->fillField('Plan[name]', 'Plan A *');
$I->checkOption("//input[@name='Plan[_planfeature][1][]'][@value=3]");
$I->checkOption("//input[@name='Plan[_planfeature][4]'][@value=6]");
$I->click('Actualizar');
$I->wait(1);

$I->see('Plan A *', 'h1');
$I->see('Activación');
$I->see('Inmediata');
$I->see('Bonificada');
$I->see('Velocidad');
$I->dontSee('Rápida');
$I->see('Lenta');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Plan A *');
