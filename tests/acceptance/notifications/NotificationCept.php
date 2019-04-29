<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Notification CRUD works');

 $I->loginAsSuperadmin();

$random = rand(111, 999);
$name = "Notificación $random";
$edit = $name . '*';

$I->clickMainMenu("Westnet","Notificaciones");

$I->makeScreenshot('westnet_notification_index');

$I->click('Alta notificación');

$I->makeScreenshot('westnet_notification_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Notification[name]', $name);
$I->selectOption('Notification[transport_id]', 'Email');

$I->click('Alta');
$I->wait(1);

$I->see($name);
$I->see('Transporte: Email');

$I->fillField('Notification[subject]', $name);
$I->fillField('Notification[content]', $name);

$I->click('Actualizar');
$I->wait(1);

$I->see('Buscar destinatarios');

$I->selectOption('Destinatary[company_id]', 'ACME');

$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('westnet_notification_view');

$I->see($name);

$I->click('Actualizar');

$I->makeScreenshot('westnet_notification_update');

$name .= '*';

$I->fillField('Notification[name]', $name);

$I->click('Actualizar');
$I->wait(1);

$I->see($edit, 'h1');

// Elimino

$I->click("Eliminar");
$I->acceptPopup();

$I->dontSee($name);
