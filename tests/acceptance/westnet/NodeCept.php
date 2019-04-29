<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$random = rand(111, 999);
$name = "Node $random";
$subnet = rand(4, 253);

$I = new WebGuy($scenario);
$I->wantTo('ensure that Node CRUD works');

 $I->loginAsAdmin();

$I->clickMainMenu("Westnet", 'Nodos');

$I->click('Alta Nodo');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Subnet no puede estar vacío.');
$I->see('Zona/Barrio no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('Node[code]', 'BAD');
$I->fillField('Node[subnet]', 'BAD');

$I->click('Alta');
$I->wait(1);

$I->see('Código debe ser un número entero.');
$I->see('Subnet debe ser mayor o igual a "1".');

$I->fillField('Node[subnet]', '123465');

$I->click('Alta');
$I->wait(1);

$I->see('Subnet debe ser menor o igual a "254".');

$I->selectOption('Node[company_id]',"ACME");
$I->fillField('Node[name]', $name);
$I->fillField('Node[code]', $random);
$I->fillField('Node[subnet]', $subnet);
$I->selectOptionForSelect2('Node[zone_id]', 'Centro');

$I->click('Alta');
$I->wait(1);

$I->see('Servidor no puede estar vacío.');

// Verifico comprobación de duplicidad

$I->fillField('Node[subnet]', '125');
$I->selectOption('Node[server_id]',"Respaldo");

$I->click('Alta');
$I->wait(1);

$I->see('Subnet "125" ya ha sido utilizado.');

// Verifico comportamiento correcto

$I->fillField('Node[subnet]', $subnet);
$I->checkOption(".//input[@type='checkbox' and @value='1']");

$I->click('Alta');
$I->wait(1);

$I->see($name, 'h1');
$I->see('Martínez');
$I->see("10.$subnet.3.2");
$I->see("10.$subnet.254.254");

// Verifico edición

$I->click('Actualizar');

$I->fillField('Node[name]', $name . ' edit');
$I->uncheckOption(".//input[@type='checkbox' and @value='1']");
$I->checkOption(".//input[@type='checkbox' and @value='2']");

$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' edit', 'h1');
$I->dontSee('Martínez');
$I->see('Alameda');

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);
