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

// Filtro por deuda

$I->click('Destinatarios');
$I->waitForText('Selección de filtros');

$I->fillField('Destinatary[debt_from]', '1000');

$I->click("Actualizar");
$I->waitForText("Información de la búsqueda");

$I->dontSee("Juan");
$I->dontSee("José");
$I->dontSee("Ana");
$I->dontSee("Betina");
$I->dontSee("César");
$I->see("Daniela");

$I->click('Destinatarios');
$I->waitForText('Selección de filtros');

$I->fillField('Destinatary[debt_from]', '');
$I->fillField('Destinatary[debt_to]', '1000');

$I->click("Actualizar");
$I->waitForText("Información de la búsqueda");

$I->see("Juan");
$I->see("José");
$I->see("Ana");
$I->see("Betina");
$I->see("César");
$I->dontSee("Daniela");

$I->click('Destinatarios');
$I->waitForText('Selección de filtros');

$I->fillField('Destinatary[debt_from]', '50');
$I->fillField('Destinatary[debt_to]', '500');

$I->click("Actualizar");
$I->waitForText("Información de la búsqueda");

$I->see("Juan");
$I->dontSee("José");
$I->dontSee("Ana");
$I->dontSee("Betina");
$I->dontSee("César");
$I->dontSee("Daniela");

// Limpio base de datos

TestDbHelper::cleanDestinataries();
