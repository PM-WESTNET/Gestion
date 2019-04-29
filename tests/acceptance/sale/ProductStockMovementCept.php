<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Product Stock Movement works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Productos", "Productos");

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium
if (Yii::$app->params['companies']['enabled']) {
    $I->selectOption('ProductSearch[stock_company_id]', 'ACME');
}
$I->wait(1); // only for selenium

$stock_text = $I->grabTextFrom(".//*[@id='grid-container']/table/tbody/tr/td[4]");
$I->see($stock_text);

$stocks = explode('|', $stock_text);
$initial_primary_stock = FormatHelper::GetFloat($stocks[0]);
$initial_secondary_stock = FormatHelper::GetFloat($stocks[1]);

$I->click('Acciones');
$I->click('Movimiento entrada');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_product_stock_movement_form');

$I->fillField('StockMovement[qty]', '23');
$I->fillField('StockMovement[secondary_qty]', '24');
$I->click('Alta');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_product_stock_movement_in');

$I->clickMainMenu("Productos", "Productos");

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->see($initial_primary_stock + 23);
$I->see($initial_secondary_stock + 24);

$I->click('Acciones');
$I->click('Movimiento salida');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_product_stock_movement_out');

$I->fillField('StockMovement[qty]', '12');
$I->fillField('StockMovement[secondary_qty]', '11');
$I->click('Alta');
$I->wait(1); // only for selenium

$I->makeScreenshot('sale_product_stock_movement_out');

$I->clickMainMenu("Productos", "Productos");

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$I->see($initial_primary_stock + 23 - 12);
$I->see($initial_secondary_stock + 24 - 11);
