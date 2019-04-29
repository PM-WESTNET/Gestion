<?php

$root = 'Ticket Status ';
$name = $root . rand(111, 999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ticket Statuses CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Tickets', 'Estados de ticket');

$I->makeScreenshot('ticket_status_index');

$I->click('Alta Estado');

$I->makeScreenshot('ticket_status_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Status[name]', $name);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('ticket_status_view');

$I->see($name);
$I->click('Actualizar');

$I->makeScreenshot('ticket_status_update');

$I->fillField('Status[name]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

// Verifico eliminación

$I->click("Eliminar");
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);