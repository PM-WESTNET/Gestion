<?php

if (Yii::$app->params['companies']['enabled']) {
    $random = rand(100, 999);
    $name = "Empresa $random";

    $I = new WebGuy($scenario);
    $I->wantTo('ensure that Company CRUD works');

    $I->loginAsSuperadmin();

    $I->clickMainMenu("Aplicación", "Empresas");

    $I->makeScreenshot('sale_company_index');

    $I->click('Alta Empresa');

    $I->makeScreenshot('sale_company_create');

    // Verifico mensaje de campos requeridos

    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Modelo de Distribución Societaria no puede estar vacío.');
    $I->see('CUIT no puede estar vacío.');
    $I->see('Código de Entidad de Pago no puede estar vacío.');

    // Verifico mensaje de campos mal ingresados

    $I->fillField('Company[email]', 'BAD');
    $I->click('Alta');
    $I->wait(1);
    $I->see('Email no es una dirección de correo válida.');

    $I->fillField('Company[name]', $name);
    $I->selectOption('Company[partner_distribution_model_id]', 'Distribución');
    $I->fillField('Company[iibb]', 'ABC');
    $I->fillField('Company[tax_identification]', '30-12345678-0');
    $I->fillField('Company[start]', '01-01-1980');
    $I->fillField('Company[email]', 'acme@fake.com');
    $I->fillField('Company[code]', $random);
    $I->click('Alta');
    $I->wait(1);
    $I->see('Tipos de comprobante no permitidos.');

    // Verifico comportamiento correcto

    $I->fillField('Company[name]', $name);
    $I->selectOption('Company[partner_distribution_model_id]', 'Distribución');
    $I->fillField('Company[iibb]', 'ABC');
    $I->fillField('Company[tax_identification]', '30-12345678-0');
    $I->fillField('Company[start]', '01-01-1980');
    $I->fillField('Company[email]', 'acme@fake.com');
    $I->fillField('Company[code]', $random);
    $I->selectOption(".//*[@id='company-billtypes']/label[2]/input", '2');
    $I->wait(1);
    $I->selectOption('Company[defaultBillType]', 'Factura B');
    $I->wait(1);
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('sale_company_view');

    // Un punto de venta requerido

    $I->fillField('PointOfSale[name]', 'Punto de Venta A');
    $I->fillField('PointOfSale[number]', '123');
    $I->checkOption("//input[@type='checkbox' and @name='PointOfSale[default]']");
    $I->click('Alta');
    $I->wait(1);

    // Busco para probar actualización

    $I->clickMainMenu("Aplicación", "Empresas");
    $I->fillColumnSearchField('CompanySearch[name]', $name);
    $I->wait(1);

    $I->click("//a[@title='Actualizar']");

    $I->makeScreenshot('sale_company_update');

    $I->seeInField('Company[name]', $name);
    $I->seeInField('Company[iibb]', 'ABC');
    $I->seeInField('Company[start]', '01-01-1980');

    $name .= ' *';

    $I->fillField('Company[name]', $name);
    $I->click('Actualizar');
    $I->wait(1);

    $I->see($name, 'h1');

    // Verifico eliminación

    $I->click('Eliminar');
    $I->acceptPopup();
    $I->wait(1);

    $I->dontSee($name);
}