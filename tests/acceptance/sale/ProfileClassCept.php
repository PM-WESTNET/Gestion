<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Profile Class CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Clientes","Perfiles adicionales");

$I->makeScreenshot('sale_profile_class_index');

$I->click('Alta Perfil adicional');

$I->makeScreenshot('sale_profile_class_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico mensaje de campos mal ingresados

$I->fillField('ProfileClass[data_min]', 'BAD');
$I->fillField('ProfileClass[data_max]', 'BAD');
$I->fillField('ProfileClass[order]', 'BAD');
$I->click('Alta');
$I->wait(1);
$I->see('Mínimo valor (o longitud mínima) debe ser un número entero.');
$I->see('Máximo valor (o longitud máxima) debe ser un número entero.');
$I->see('Órden debe ser un número entero.');

// Verifico comportamiento correcto

$I->fillField('ProfileClass[name]', 'Perfil A');
$I->fillField('ProfileClass[data_min]', '');
$I->fillField('ProfileClass[data_max]', '');
$I->fillField('ProfileClass[order]', '');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_profile_class_view');

$I->see('Perfil A', 'h1');
$I->click('Actualizar');

$I->makeScreenshot('sale_profile_class_update');

$I->fillField('ProfileClass[name]', 'Perfil A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Perfil A *', 'h1');
$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Perfil A *');