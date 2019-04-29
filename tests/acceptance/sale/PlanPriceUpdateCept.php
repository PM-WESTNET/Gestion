<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Plan Price Update works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Productos","Planes");

$I->click(" Actualizar precios");
$I->wait(1); // only for selenium

$I->fillField('//input[@data-model-id=3]', '130');
$I->pressKey("//input[@data-model-id=3]", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->see('$157,30', 'td'); // Precio final Básico

$I->fillField('//input[@data-model-id=4]', '610');
$I->pressKey("//input[@data-model-id=4]", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->see('$738,10', 'td'); // Precio final VIP

$I->click("Actualizar precios en lote");
$I->wait(1); // only for selenium
$I->fillField('UpdatePriceFormModel[percentage]', '25');
$I->click("Actualizar");
$I->wait(1); // only for selenium

$I->see('Se ha actualizado el precio de 3 items de forma satisfactoria.');

$I->see('$196,62', 'td'); // Precio final Básico
$I->see('$922,62', 'td'); // Precio final VIP

$I->click("Lista de planes");
$I->wait(1); // only for selenium

$I->see('$162,50', 'td'); // Precio neto Básico
$I->see('$196,62', 'td'); // Precio final Básico
$I->see('$762,50', 'td'); // Precio neto VIP
$I->see('$922,62', 'td'); // Precio final VIP

// Volver a datos originales

$I->click("Actualizar precios");
$I->wait(1); // only for selenium

$I->fillField('//input[@data-model-id=3]', '100');
$I->pressKey("//input[@data-model-id=3]", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField('//input[@data-model-id=4]', '600');
$I->pressKey("//input[@data-model-id=4]", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
