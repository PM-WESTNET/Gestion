<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Invoice Class CRUD works');

 $I->loginAsSuperadmin();

$I->clickMainMenu("Comprobantes", "Config", "Clases Facturación E.");

$I->makeScreenshot('sale_invoice_class_index');

$I->click('Alta Clase Facturación E.');

$I->makeScreenshot('sale_invoice_class_create');

// Verifico mensaje de campos requeridos

$I->click('Alta');
$I->wait(1);
$I->see('Clase no puede estar vacío.');
$I->see('Nombre no puede estar vacío.');

// Verifico comportamiento correcto

$I->fillField('InvoiceClass[class]', '/Temporal');
$I->fillField('InvoiceClass[name]', 'Clase A');
$I->click('Alta');
$I->wait(1);

$I->see('Invalid class.');
$I->fillField('InvoiceClass[class]', 'app\controllers\SiteController');
$I->click('Alta');
$I->wait(1);

$I->makeScreenshot('sale_invoice_class_view');

$I->see('Clase A', 'h1');
$I->see('app\controllers\SiteController', 'td');

$I->makeScreenshot('sale_invoice_class_update');

$I->click('Actualizar');
$I->fillField('InvoiceClass[name]', 'Clase A *');
$I->click('Actualizar');
$I->wait(1);

$I->see('Clase A *', 'h1');

$I->click('Eliminar');
$I->acceptPopup();

$I->wait(1);

$I->dontSee('Clase A *');
