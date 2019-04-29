<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that proveedores menu works in every item');

 $I->loginAsSuperadmin();

//$I->dontSeeVisualChanges('mainMenu', '#main-menu');

$I->click('Westnet');
$I->seeInTitle('Westnet');
$I->click("//div[@id='wide-navbar']/ul/li/a[contains(text(), 'Home')]");
$I->seeInTitle('Westnet');

$I->checkMenuItem('Proveedores', 'Proveedores');
$I->checkMenuItem('Proveedores', 'Deuda a Proveedores');
$I->checkMenuItem('Proveedores', 'Comprobantes de proveedor');
$I->checkMenuItem('Proveedores', 'Pagos a proveedor');
$I->checkMenuItem('Proveedores', 'Facturas y Pagos de Proveedores');
