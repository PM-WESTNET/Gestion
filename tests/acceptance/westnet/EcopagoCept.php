<?php

$random = rand(1111, 9999);
$name = 'Ecopago ' . $random;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Ecopago CRUD works');

 $I->loginAsAdmin();

$I->clickMainMenu("Westnet", "Ecopagos");

$I->click('Alta Ecopago');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Estado no puede estar vacío.');
$I->see('Tipo de comisión no puede estar vacío.');
$I->see('Valor de la comisión no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Ecopago[name]', $name);
$I->fillField('Ecopago[description]', $name);
$I->fillField('Ecopago[limit]', 1000);
$I->fillField('Ecopago[number]', $random);
$I->selectOption('Ecopago[status_id]', 'Habilitado');
$I->selectOption('Ecopago[commission_type]', 'Fija');
$I->fillField('Ecopago[commission_value]', 90);
$I->selectOptionForSelect2('Ecopago[account_id]', 'Caja Pesos');
$I->click('Alta');
$I->waitForText('éxito');

$I->see($name, 'h1');
$I->click('Actualizar');
$I->waitForText('Actualizar Ecopago');

$I->fillField('Ecopago[name]', $name . ' edited');
$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' edited', 'h1');

// Eliminar está deshabilitado para todas las empresas, se elimina manualmente
TestDbHelper::execute("DELETE FROM `commission` where `ecopago_id` in (SELECT `ecopago_id` FROM `ecopago` where `name` like 'Ecopago%')", 'ecopago');
TestDbHelper::execute("DELETE FROM `ecopago` where `name` like 'Ecopago%'", 'ecopago');

$I->wait(1);

$I->clickMainMenu("Westnet", "Ecopagos");
$I->dontSee($name . ' edited');
