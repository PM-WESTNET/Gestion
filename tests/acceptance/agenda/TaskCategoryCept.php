<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Task Category CRUD works');

if (Yii::$app->params['agenda_enabled']) {
    $I->loginAsSuperadmin();

    $I->clickMainMenu('Agenda', 'Categorías de Tarea');

    $I->makeScreenshot('agenda_task_category_index');

    $I->click('Alta Categoría');

    $I->makeScreenshot('agenda_task_category_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Nombre del Sistema no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('Category[name]', 'Categoría A');
    $I->fillField('Category[slug]', 'slug');
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('agenda_task_category_view');

    $I->see('Categoría A', 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('agenda_task_category_update');

    $I->fillField('Category[name]', 'Categoría A *');
    $I->click('Actualizar');
    $I->wait(1);

    $I->see('Categoría A *', 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->dontSee('Categoría A *');
}
