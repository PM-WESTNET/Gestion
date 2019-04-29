<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Product Stock Update works');

$I->loginAsSuperadmin();

$I->clickMainMenu("Productos","Productos");

$I->click("Ingreso de Stock");
$I->wait(1); // only for selenium

$I->fillField('#search_text', 'man');
$I->pressKey('#search_text', WebDriverKeys::ENTER);
$I->wait(1); // only for selenium

$stock_text = $I->grabTextFrom(".//*[@id='grid']/table/tbody/tr[1]/td[5]");
$stocks = explode('|', $stock_text);
$initial_primary_stock = FormatHelper::GetFloat($stocks[0]);
$initial_secondary_stock = FormatHelper::GetFloat($stocks[1]);

$I->makeScreenshot('sale_product_stock_update');

$I->fillField('StockMovement[qty]', '23');
$I->fillField('StockMovement[secondary_qty]', '24');
$I->click(".//*[@id='form0']/div[1]/div[3]/button");
$I->wait(1);

$I->see(($initial_primary_stock + 23) . 'kg');
$I->see(($initial_secondary_stock + 24) . 'u');

$I->fillField('StockMovement[qty]', '23');
$I->fillField('StockMovement[secondary_qty]', '24');
$I->click(".//*[@id='form0']/div[1]/div[4]/button");
$I->wait(1);
$I->click(".//*[@id='form0']/div[1]/div[3]/button");
$I->wait(1);

$I->see(($initial_primary_stock + 23) . 'kg');
$I->see(($initial_secondary_stock + 24) . 'u');

$I->fillField('StockMovement[qty]', '-23');
$I->fillField('StockMovement[secondary_qty]', '-24');
$I->click(".//*[@id='form0']/div[1]/div[3]/button");
$I->wait(1);

$I->see(($initial_primary_stock) . 'kg');
$I->see(($initial_secondary_stock) . 'u');
