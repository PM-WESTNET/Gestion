<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that destinataries by filter works');

$dbhelper = new TestDbHelper();
$dbhelper->initializeDb($I);

 $I->loginAsSuperadmin();

$I->clickMainMenu("Westnet", "Notificaciones");
$I->waitForText('Por email');

$I->fillColumnSearchField('NotificationSearch[name]', 'email');
$I->wait(1);

$I->click("//a[@title='Ver']");

$I->waitForText('Por email (Creada)');

$I->click("Elegir destinatarios");
$I->wait(1);

$I->click("//a[@title='Ver']");

$I->waitForText("Cantidad actual:");
$I->see("Juan");
$I->see("José");
$I->see("Ana");
$I->see("Betina");
$I->see("César");
$I->see("Daniela");

// Filtro por empresa

$I->click('Destinatarios');

$I->addOptionForSelect2("Destinatary[_companies][]", "Metro");

$I->click("Actualizar");
$I->waitForText("Información de la búsqueda");

$I->dontSee("Juan");
$I->see("José");
$I->dontSee("Ana");
$I->dontSee("Betina");
$I->dontSee("César");
$I->see("Daniela");

// Limpio base de datos

TestDbHelper::cleanDestinataries();
