<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda/Notification CRUD works');

if (Yii::$app->params['agenda_enabled']) {

    $I->loginAsUser();

    $initial_notifications = $I->grabTextFrom("(//span[@class='badge'])[1]");
    $initial_notifications = strval($initial_notifications);

    // Creo varias tareas

    $task_to_create = 2;

    for ($count = 0; $count < $task_to_create; $count++) {
        $I->clickMainMenu('Agenda', 'Tareas');

        $I->click("//a[contains(@class, 'btn')  and contains(text(), 'Alta Tarea')]");

        $I->fillField('Task[name]', 'Notificacion ' . $count);
        $I->checkOption(".//*[@id='task-assignallusers']");
        $I->fillField('Task[date]', date('d-m-Y'));
        $I->selectOption('Task[task_type_id]', 'Tarea global');
        $I->click('Alta');
        $I->wait(1);
    }

    $I->clickMainMenu('Clientes', 'Zonas');

    $I->see($initial_notifications + $task_to_create, "(//span[@class='badge'])[1]");

    $I->makeScreenshot('agenda_notification_collapsed');

    $I->click("(//div[@class='agenda-notifications']/..)[1]");
    $I->wait(1);

    $I->makeScreenshot('agenda_notification_full');

    $I->click("Marcar todo como leido");
    $I->wait(1);

    $I->seeElementInDOM("(//span[@class='badge' and not(text())])[1]");

    TestDbHelper::execute("DELETE FROM `notification` where `task_id` in (SELECT `task_id` FROM `task` where `name` like 'Notificacion %')", 'agenda');
    TestDbHelper::execute("DELETE FROM `task` where `name` like 'Notificacion %'", 'agenda');
}
