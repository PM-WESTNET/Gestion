<?php

$random = rand(111, 999);
$name = "status $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Event Type CRUD works');

if (Yii::$app->params['agenda_enabled']) {
    $I->loginAsSuperadmin();

    $I->wait(2);

    $I->clickMainMenu('Agenda', 'Tipos de Evento');

    $I->makeScreenshot('agenda_event_type_index');

    $I->click('Alta Tipo de Evento');

    $I->makeScreenshot('agenda_event_type_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Nombre del Sistema no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('EventType[name]', $name);
    $I->fillField('EventType[slug]', $name);
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('agenda_event_type_view');

    $I->see($name, 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('agenda_event_type_update');

    $name .= '*';

    $I->fillField('EventType[name]', $name);
    $I->click('Actualizar');
    $I->wait(1);

    $I->see($name, 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->dontSee($name);
}
