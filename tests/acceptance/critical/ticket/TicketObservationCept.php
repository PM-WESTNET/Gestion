<?php

$root = 'Ticket ';
$name = $root . rand(111, 999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Tickets Observations CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Tickets', 'Tickets');

$I->click("//a[contains(@class, 'btn')  and contains(text(), 'Alta Ticket')]");

// Creo ticket

$I->selectOptionForSelect2('Ticket[category_id]', 'Administrativo');
$I->selectOptionForSelect2('Ticket[customer_id]', 'Juan');
$I->wait(1);
$I->selectOption('Ticket[contract_id]', 'Primero');
$I->fillField('Ticket[title]', $name);
$I->fillField('Ticket[content]', $name);
$I->selectOption('Ticket[status_id]', 'Activo');
$I->click('Alta');
$I->wait(1);

$I->click('Observaciones');

// Verifico campos requeridos

$I->click('Alta observación');
$I->wait(1);

$I->see('Título no puede estar vacío.');
$I->see('Descripción no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Observation[title]', 'aaaaaaaa');
$I->fillField('Observation[description]', 'aaaaaaaa');

$I->click('Alta observación');
$I->wait(1);

$I->see('Observación guardada con éxito!');

$I->fillField('Observation[title]', 'bbbbbbb');
$I->fillField('Observation[description]', 'bbbbbbb');

$I->click('Alta observación');
$I->wait(1);

$I->see('aaaaaaaa');
$I->see('bbbbbbb');

$I->makeScreenshot('ticket_observation');

// Verifico eliminación

$I->clickMainMenu('Tickets', 'Tickets');
$I->click('Filtros');
$I->wait(1);
$I->fillField('TicketSearch[title]', $name);
$I->click('Buscar');

$I->click(".//a[@title = 'Eliminar']");
$I->acceptPopup();

$I->wait(1);

$I->waitForText('No se encontraron resultados');
$I->dontSee($name);