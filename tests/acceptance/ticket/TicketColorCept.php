<?php

$root = 'Ticket Color ';
$name = $root . rand(111, 999);
$color = rand(111111, 999999);
$order = rand(1, 10);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ticket Colors CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Tickets', 'Colores de ticket');

$I->makeScreenshot('ticket_color_index');

$I->click('Alta Color');

$I->makeScreenshot('ticket_color_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Código de color no puede estar vacío.');
$I->see('Order no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Color[name]', $name);
$I->fillField('Color[color]', $color);
$I->fillField('Color[order]', $order);
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('ticket_color_view');

$I->see($name);
$I->click('Actualizar');

$I->makeScreenshot('ticket_color_update');

$I->fillField('Color[name]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

// Verifico eliminación

$I->click("Eliminar");
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);
