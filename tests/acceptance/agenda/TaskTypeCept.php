<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Task Type CRUD works');

if (Yii::$app->params['agenda_enabled']) {
    $I->loginAsSuperadmin();

    $I->wait(2);

    $I->clickMainMenu('Agenda', 'Tipos de Tarea');

    $I->makeScreenshot('agenda_task_type_index');

    $I->click('Alta Tipo de Tarea');

    $I->makeScreenshot('agenda_task_type_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Nombre del Sistema no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('TaskType[name]', 'Tipo A');
    $I->fillField('TaskType[slug]', 'slug');
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('agenda_task_type_view');

    $I->see('Tipo A', 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('agenda_task_type_update');

    $I->fillField('TaskType[name]', 'Tipo A *');
    $I->click('Actualizar');
    $I->wait(1);

    $I->see('Tipo A *', 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->dontSee('Tipo A *');
}
