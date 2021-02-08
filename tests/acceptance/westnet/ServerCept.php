<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$random = rand(111, 999);
$name = "Server $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Server CRUD works');

 $I->loginAsAdmin();

$I->clickMainMenu("Westnet", 'Servidores');

$I->click('Alta Servidor');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Url no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Server[name]', $name);
$I->selectOption('Server[status]',"enabled");
$I->fillField('Server[url]',"http://www.ejemplo.com.ar/");

$I->click('Alta');
$I->wait(1);

$I->see($name, 'h1');
$I->click('Actualizar');

$I->fillField('Server[name]', $name . ' edit');

$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' edit', 'h1');

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);
