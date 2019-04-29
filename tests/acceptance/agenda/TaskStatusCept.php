<?php

$random = rand(111, 999);
$name = "status $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Task Status CRUD works');

if (Yii::$app->params['agenda_enabled']) {
    $I->loginAsSuperadmin();

    $I->clickMainMenu('Agenda', 'Estados de Tarea');

    $I->makeScreenshot('agenda_task_status_index');

    $I->click('Alta Estado de Tarea');

    $I->makeScreenshot('agenda_task_status_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Nombre del Sistema no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('Status[name]', $name);
    $I->fillField('Status[slug]', "slug$random");
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('agenda_task_status_view');

    $I->see($name, 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('agenda_task_status_update');

    $name .= '*';

    $I->fillField('Status[name]', $name);
    $I->click('Actualizar');
    $I->wait(1);

    $I->see($name, 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->dontSee($name);
}
