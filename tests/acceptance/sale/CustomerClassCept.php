<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Customer Class CRUD works');

if (Yii::$app->params['class_customer_required']) {
    $I->loginAsSuperadmin();

    $I->clickMainMenu("Clientes","Categorías de Cliente");

    $I->makeScreenshot('sale_custumer_class_index');

    $I->click('Alta Categoría de Cliente');

    $I->makeScreenshot('sale_custumer_class_create');

    // Verifico mensaje de campos requeridos
    
    $I->click('Alta');
    $I->wait(1);
    $I->see('Nombre no puede estar vacío.');
    $I->see('Identificador Sequre no puede estar vacío.');
    $I->see('Días de aviso de corte no puede estar vacío.');
    $I->see('Porcentaje a Facturar no puede estar vacío.');
    $I->see('Dias para corte no puede estar vacío.');
    $I->see('Porcentaje de Tolerancia de Deuda no puede estar vacío.');

    // Verifico mensaje de campos mal ingresados
    
    $I->fillField('CustomerClass[code_ext]', 'BAD');
    $I->fillField('CustomerClass[tolerance_days]', 'BAD');
    $I->fillField('CustomerClass[percentage_bill]', 'BAD');
    $I->fillField('CustomerClass[days_duration]', 'BAD');
    $I->fillField('CustomerClass[percentage_tolerance_debt]', 'BAD');
    $I->click('Alta');
    $I->wait(1);
    $I->see('Identificador Sequre debe ser un número entero.');
    $I->see('Días de aviso de corte debe ser un número entero.');
    $I->see('Porcentaje a Facturar debe ser un número entero.');
    $I->see('Dias para corte debe ser un número entero.');
    $I->see('Porcentaje de Tolerancia de Deuda debe ser un número entero.');

    // Verifico comportamiento correcto

    $I->fillField('CustomerClass[name]', 'Categoría A');
    $I->fillField('CustomerClass[code_ext]', '123');
    $I->fillField('CustomerClass[tolerance_days]', '14');
    $I->fillField('CustomerClass[percentage_bill]', '100');
    $I->fillField('CustomerClass[days_duration]', '30');
    $I->fillField('CustomerClass[percentage_tolerance_debt]', '50');
    $I->click(".//*[@name='customerclass-colour-source']/../div[contains(@class,'sp-replacer')]");
    $I->click("//span[@style = 'background-color:rgb(255, 0, 255);']");
    $I->click('Alta');
    $I->wait(1);

    $I->makeScreenshot('sale_custumer_class_view');

    $I->see('Categoría A', 'h1');
    $I->click('Actualizar');

    $I->makeScreenshot('sale_custumer_class_update');

    $I->fillField('CustomerClass[name]', 'Categoría A *');
    $I->click('Actualizar');
    $I->wait(1);

    $I->see('Categoría A *', 'h1');
    $I->click('Eliminar');
    $I->acceptPopup();

    $I->wait(1);

    $I->dontSee('Categoría A *');
}