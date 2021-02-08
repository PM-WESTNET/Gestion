<?php

$random = rand(111, 999);
$name = "task $random";

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Task CRUD works');

if (Yii::$app->params['agenda_enabled']) {

    $I->loginAsUser();

    $I->clickMainMenu('Agenda', 'Tareas');

    $I->makeScreenshot('agenda_task_index');

    $I->click("//a[contains(@class, 'btn')  and contains(text(), 'Alta Tarea')]");

    $I->makeScreenshot('agenda_task_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Fecha no puede estar vacío.');
    $I->see('Tipo de tarea no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('Task[name]', $name);
    $I->selectOption('Task[task_type_id]', 'Tarea global');
    $I->checkOption(".//*[@id='task-assignallusers']");
    $I->fillField('Task[date]', date('d-m-Y'));
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('agenda_task_view');

    $I->waitForText($name);

    $I->click('Actualizar');

    $I->makeScreenshot('agenda_task_update');

    $I->see('superadmin', 'label');

    $name .= '*';

    $I->fillField('Task[name]', $name);
    $I->click('Actualizar');
    $I->wait(1);

    $I->see($name, 'h1');

// Verifico que esté en el calendario

    $I->clickMainMenu('Agenda', 'Mi agenda');

    $I->click(".//*[@id='created_by_me']/..");
    $I->click('Filtrar');
    $I->wait(1);

    $I->see($name, 'span');

    TestDbHelper::execute("DELETE FROM `notification` where `task_id` in (SELECT `task_id` FROM `task` where `name` like 'Tarea A%')", 'agenda');
    TestDbHelper::execute("DELETE FROM `task` where `name` like 'Tarea A%'", 'agenda');
}
