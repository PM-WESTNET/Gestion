<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$I = new WebGuy($scenario);
$I->wantTo('ensure that forced connection are limited to 2');

 $I->loginAsSuperadmin();

// Realizo limpieza

TestDbHelper::execute("DELETE FROM `connection_forced_historial`");
TestDbHelper::execute("UPDATE `connection` SET `status` = 'disabled'");

// Comienzo a probar

$I->clickMainMenu("Clientes", 'Clientes');

$I->click('Filtrar');
$I->wait(1);

$I->click("(//a[@title='Ver'])[1]");
$I->wait(1);

$I->click("(//a[@title='Ver'])[1]");
$I->wait(1);

$allowed_count = 2;

for ($i = 1; $i <= $allowed_count; $i++) {
    $I->click("Forzar Activación");
    $I->acceptPopup();
    $I->waitForText('Fecha de vencimiento de Activación Forzada');

    $I->fillField('due_date', date('d-m-Y'));
    $I->fillField(".//*[@id='reason']", 'Random ' . rand(111, 999));

    $I->click(".//*[@id='connection-modal']/div/div/div[3]/button[2]");
    $I->waitForText('Forzada');

    $I->click("Desactivar");
    $I->acceptPopup();
    $I->waitForText('Deshabilitada', 40);
}

$I->click("Forzar Activación");
$I->acceptPopup();

$I->waitForText('Fecha de vencimiento de Activación Forzada');

$I->fillField('due_date', date('d-m-Y'));
$I->fillField(".//*[@id='reason']", 'Random ' . rand(111, 999));

$I->click(".//*[@id='connection-modal']/div/div/div[3]/button[2]");

$I->waitForText('No se puede forzar esta conexión porque ha superado el limite de forzado por mes');

// Realizo limpieza

TestDbHelper::execute("DELETE FROM `connection_forced_historial`");
TestDbHelper::execute("UPDATE `connection` SET `status` = 'disabled'");
