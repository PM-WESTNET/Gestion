<?php

// En esta prueba considera la existencia de un tipo de entidad llamada banco

$I = new WebGuy($scenario);
$I->wantTo('ensure that Print ADS works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Clientes", 'Clientes');
$I->click('Filtros');
$I->wait(1);
$I->fillField('CustomerSearch[search_text]', 'Juan');
$I->pressKey(".//*[@id='search_text']", WebDriverKeys::ENTER);
$I->wait(1);

$I->click(".//a[@title = 'Ver']");
$I->wait(1);

// Veo contrato inactivo
$I->click("(//a[@title = 'Ver'])[2]");
$I->wait(1);

$I->click("Imprimir ADS");

$I->wait(2);

$I->executeInSelenium(function ($webdriver) {
     $handles = $webdriver->getWindowHandles();
     $last_window = end($handles);
     $webdriver->switchTo()->window($last_window);
});

$I->seeElementInDOM(".//*[@class='pdfViewer']");
