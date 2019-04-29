<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Product Price Update works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Productos","Productos");

$I->click(" Actualizar precios");
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->fillField(".//*[@id='input-net0']", '13');
$I->pressKey(".//*[@id='input-net0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'per');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->fillField(".//*[@id='input-net0']", '15');
$I->click(".//*[@id='form0']/div[1]/div[3]/button");
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'per');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->clickMainMenu("Productos","Productos");

$I->see('$13,00'); // Precio neto manzana
$I->see('$15,73'); // Precio final manzana
$I->see('$15,00'); // Precio neto pera
$I->see('$18,15'); // Precio final pera

$I->click(" Actualizar precios");
$I->wait(1); // only for selenium

$I->click("Actualizar precios en lote");
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_product_price_update');

$I->fillField('UpdatePriceFormModel[percentage]', '25');
$I->click("Actualizar");
$I->wait(1); // only for selenium

$I->see('Se ha actualizado el precio de 4 items de forma satisfactoria.');

$I->clickMainMenu("Productos","Productos");

$I->see('$16,25', 'td'); // Precio neto manzana
$I->see('$19,66', 'td'); // Precio final manzana
$I->see('$18,75', 'td'); // Precio neto pera
$I->see('$22,69', 'td'); // Precio final pera

// Volver a datos originales

$I->click("Actualizar precios");
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField(".//*[@id='input-net0']", '10');
$I->pressKey(".//*[@id='input-net0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'per');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
$I->fillField(".//*[@id='input-net0']", '12');
$I->pressKey(".//*[@id='input-net0']", WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
