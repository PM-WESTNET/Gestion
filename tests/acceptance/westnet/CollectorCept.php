<?php

$random =  rand(1111, 9999);
$name = 'Collector ' . $random;
$username = 'user'.$random;

$I = new WebGuy($scenario);
$I->wantTo('ensure that Collector CRUD works');

 $I->loginAsAdmin();

$I->clickMainMenu("Westnet","Recaudadores");

$I->click('Crear recaudador');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Nombre no puede estar vacío.');
$I->see('Apellido no puede estar vacío.');
$I->see('Código no puede estar vacío.');
$I->see('Contraseña no puede estar vacío.');
$I->see('Repetir contraseña no puede estar vacío.');
$I->see('Tipo de documento no puede estar vacío.');
$I->see('Número de documento no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('Collector[name]', $name);
$I->fillField('Collector[lastname]', $name);
$I->fillField('Collector[number]', '321'); // Ya usado
$I->fillField('Collector[password]', $username);
$I->fillField('Collector[password_repeat]', $random);
$I->selectOption('Collector[document_type]', 'DNI');
$I->fillField('Collector[document_number]', $random);
$I->fillField('Collector[limit]', 1000);

$I->click('Alta');
$I->wait(1);

$I->see('Las contraseñas no coinciden.');

$I->fillField('Collector[password_repeat]', $username);

$I->click('Alta');
$I->wait(1);

$I->see('Código "321" ya ha sido utilizado.');

$I->fillField('Collector[number]', $random);

$I->click('Alta');
$I->wait(1);

$I->see($name, 'h1');

// Verifico edición

$I->click('Actualizar');

$I->fillField('Collector[name]', $name . ' edited');
$I->click('Actualizar');
$I->wait(1);

$I->see($name . ' edited', 'h1');

// Verifico asignación

$I->clickMainMenu("Westnet","Ecopagos");

$I->click("(//a[@title = 'Ver'])[1]");
$I->wait(2);

$I->click('Administrar recaudadores');

$I->see($name);

try{
    for($j = 1; $j< 100; $j++){
        $I->checkOption("(//input[@type = 'checkbox'])[$j]");
    }
} catch (Exception $ex) {

}

$I->click('Actualizar');
$I->wait(1);

$I->see('Cambios en recaudadores guardados con éxito!');


// Verifico eliminación

$I->clickMainMenu("Westnet","Recaudadores");

$I->click("//td[contains(text(),'$random')]/../td/a[@title = 'Ver']");
$I->wait(1);

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee($name);

TestDbHelper::execute("DELETE FROM `assignation` where `ecopago_id` = 2", 'ecopago');
