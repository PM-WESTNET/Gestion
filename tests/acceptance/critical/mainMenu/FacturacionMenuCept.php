<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that facturacion menu works in every item');

 $I->loginAsSuperadmin();

//$I->dontSeeVisualChanges('mainMenu', '#main-menu');

$I->click('Westnet');
$I->seeInTitle('Westnet');
$I->click("//div[@id='wide-navbar']/ul/li/a[contains(text(), 'Home')]");
$I->seeInTitle('Westnet');

$I->checkMenuItem('Facturación', 'Factura A');
$I->checkMenuItem('Facturación', 'Factura B');
$I->checkMenuItem('Facturación', 'Factura C');
$I->checkMenuItem('Facturación', 'Presupuesto');
$I->checkMenuItem('Facturación', 'Nota Crédito A');
$I->checkMenuItem('Facturación', 'Nota Crédito B');
$I->checkMenuItem('Facturación', 'Nota Crédito C');
$I->checkMenuItem('Facturación', 'Nota Débito A');
$I->checkMenuItem('Facturación', 'Nota Débito B');
$I->checkMenuItem('Facturación', 'Nota Débito C');
$I->checkMenuItem('Facturación', 'Remito');
$I->checkMenuItem('Facturación', 'Pedido');
$I->checkMenuItem('Facturación', 'Facturación por Lotes');
