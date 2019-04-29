<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer Class CRUD works');

if (Yii::$app->params['category_customer_required']) {
    $I->loginAsSuperadmin();

    $I->clickMainMenu("Clientes", "Rubros de Cliente");

    $I->makeScreenshot('sale_custumer_category_index');

    $I->click('Alta Rubro de Cliente');

    $I->makeScreenshot('sale_custumer_category_create');

    // Verifico mensaje de campos requeridos
    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Estado no puede estar vacío.');

    // Verifico comportamiento correcto

    $I->fillField('CustomerCategory[name]', 'Rubro A');
    $I->selectOption('CustomerCategory[status]', 'Enabled');
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('sale_custumer_category_view');

    $I->see('Rubro A', 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('sale_custumer_category_update');

    $I->fillField('CustomerCategory[name]', 'Rubro Madre');
    $I->click('Actualizar');
    $I->wait(1);

    $I->see('Rubro Madre', 'h1');

// Verifico creación de hijos

    $I->clickMainMenu("Clientes", "Rubros de Cliente");
    $I->click('Alta Rubro de Cliente');
    $I->fillField('CustomerCategory[name]', 'Rubro Hijo');
    $I->selectOption('CustomerCategory[status]', 'Enabled');
    $I->selectOption('CustomerCategory[parent_id]', 'Rubro Madre');
    $I->click('Alta');
    $I->wait(1);

    $I->clickMainMenu("Clientes", "Rubros");
    $I->click('Alta Rubro');
    $I->fillField('CustomerCategory[name]', 'Rubro Hija');
    $I->selectOption('CustomerCategory[status]', 'Enabled');
    $I->selectOption('CustomerCategory[parent_id]', 'Rubro Madre');
    $I->click('Alta');
    $I->wait(1);

    $I->clickMainMenu("Clientes", "Rubros");
    $I->wait(1);

    $I->see('Rubro Madre');
    $I->see('Rubro Hijo');
    $I->see('Rubro Hija');

// Elimino hijos y madre

    $I->fillField('CustomerCategorySearch[name]', 'Rubro');
    $I->pressKey("//input[@name='CustomerCategorySearch[name]']", WebDriverKeys::ENTER);
    $I->wait(1);
    $I->click("//a[@title='Eliminar']");
    $I->acceptPopup();
    $I->wait(1);

    $I->fillField('CustomerCategorySearch[name]', 'Rubro');
    $I->pressKey("//input[@name='CustomerCategorySearch[name]']", WebDriverKeys::ENTER);
    $I->wait(1);
    $I->click("//a[@title='Eliminar']");
    $I->acceptPopup();
    $I->wait(1);

    $I->fillField('CustomerCategorySearch[name]', 'Rubro');
    $I->pressKey("//input[@name='CustomerCategorySearch[name]']", WebDriverKeys::ENTER);
    $I->wait(1);
    $I->click("//a[@title='Eliminar']");
    $I->acceptPopup();

    $I->dontSee('Rubro Madre');
    $I->dontSee('Rubro Hijo');
    $I->dontSee('Rubro Hija');
}