<?php

$root = 'Ticket ';
$name = $root . rand(111, 999);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Tickets CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu('Tickets', 'Tickets');

$I->makeScreenshot('ticket_index');

$I->click("//a[contains(@class, 'btn')  and contains(text(), 'Alta Ticket')]");
$I->wait(1);

$I->makeScreenshot('ticket_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Cliente no puede estar vacío.');
$I->see('Título no puede estar vacío.');
$I->see('Contenido no puede estar vacío.');

// Verifico comportamiento correcto

$I->selectOptionForSelect2('Ticket[customer_id]', 'Juan');
$I->selectOptionForSelect2('Ticket[category_id]', 'Administrativo');
$I->fillField('Ticket[title]', $name);
$I->fillField('Ticket[content]', $name);
$I->selectOption('Ticket[status_id]', 'Activo');
$I->click("Alta");
$I->waitForText($name);

$I->click('Actualizar');

$I->fillField('Ticket[title]', $name . ' *');
$I->click('Actualizar');
$I->wait(1);

// Verifico tarea en agenda

$I->clickMainMenu('Home');
$I->clickMainMenu('Agenda', 'Tareas');

$I->fillColumnSearchField('TaskSearch[name]', $name);

$I->see($name);
$I->see('Pendiente', 'td');

// Verifico filtrado

$I->clickMainMenu('Tickets', 'Tickets');
$I->click('Filtros');
$I->wait(1);
$I->fillField('TicketSearch[title]', $name);
$I->click('Buscar');

$I->see($name);
$I->see('Activo');

// Verifico cerrado

$I->click(".//a[@title = 'Ver']");
$I->click("Cerrar ticket");
$I->wait(1);

$I->see('Ticket cerrado con éxito!');

// Verifico tarea en agenda

$I->clickMainMenu('Home');
$I->clickMainMenu('Agenda', 'Tareas');

$I->fillColumnSearchField('TaskSearch[name]', $name);

$I->see($name);
$I->see('Completada', 'td');

// Verifico visualización en tickets

$I->clickMainMenu('Tickets', 'Tickets');

$I->dontSee($name);

$I->click('Filtros');
$I->wait(1);
$I->selectOption('TicketSearch[status_id]', 'Cerrado');
$I->wait(1);
$I->fillColumnSearchField('TicketSearch[title]', $name);

$I->see($name);

//( Verifico apertura

$I->click(".//a[@title = 'Ver']");

$I->click("Reabrir ticket");
$I->wait(1);

$I->see('Ticket reabierto con éxito!');

$I->clickMainMenu('Tickets', 'Tickets');
$I->click('Filtros');
$I->wait(1);
$I->fillField('TicketSearch[title]', $name);
$I->click('Buscar');

$I->see($name);
$I->see('Activo');

// Verifico eliminación

$I->click(".//a[@title = 'Eliminar']");
$I->wait(1);
$I->click('De acuerdo');

$I->wait(1);

$I->dontSee($name);