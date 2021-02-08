<?php

$root = 'Ticket Category ';
$name = $root . rand(111, 999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ticket Categories CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Tickets', 'Categorías de ticket');

$I->makeScreenshot('ticket_category_index');

$I->click('Alta categoría');

$I->makeScreenshot('ticket_category_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Nombre de sistema no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Category[name]', $name);
$I->fillField('Category[slug]', 'xxx');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('ticket_category_view');

$I->see($name);
$I->click('Actualizar');

$I->makeScreenshot('ticket_category_update');

$I->fillField('Category[name]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

// Verifico eliminación

$I->click("Eliminar");
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);