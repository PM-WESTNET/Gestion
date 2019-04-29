<?php

if (Yii::$app->params['companies']['enabled']) {
    $I = new WebGuy($scenario);
    $I->wantTo('ensure that Point of Sale CRUD works');

    $I->loginAsSuperadmin();

    // Creo una empresa temporal
    $I->clickMainMenu("Aplicación", "Empresas");
    $I->fillField('CompanySearch[name]', 'ACME');
    $I->pressKey("//input[@name='CompanySearch[name]']", WebDriverKeys::ENTER);
    $I->wait(1);
    $I->click("//*[@title='Ver']");
    $I->wait(1);

    // Inicio pruebas sobre puntos de venta
    $I->click('Alta Punto de Venta');
    $I->see('Alta Punto de Venta');

    $I->makeScreenshot('sale_point_of_sale_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Número no puede estar vacío.');

    // Verifico mensaje de campos mal ingresados

    $I->fillField('PointOfSale[number]', 'BAD');
    $I->click('Alta');
    $I->wait(1);
    $I->see('Número debe ser un número entero.');

    // Verifico comportamiento correcto

    $I->fillField('PointOfSale[name]', 'Punto de Venta A');
    $I->fillField('PointOfSale[number]', rand(111, 999));
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('sale_point_of_sale_view');

    $I->see('Punto de Venta A', 'h1');
    $I->see('Empresa A');
    $I->click('Actualizar');

    $I->makeScreenshot('sale_point_of_sale_update');

    $I->fillField('PointOfSale[name]', 'Punto de Venta A *');
    $I->click('Actualizar');
    $I->wait(1);

    $I->see('Punto de Venta A *', 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->makeScreenshot('sale_point_of_sale_index');

    $I->dontSee('Punto de Venta A *');
}
