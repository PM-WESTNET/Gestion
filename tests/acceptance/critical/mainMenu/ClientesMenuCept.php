<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that clientes menu works in every item');

 $I->loginAsSuperadmin();
$I->checkMenuItem('Clientes', 'Clientes');
$I->checkMenuItem('Clientes', 'Deudores');
$I->checkMenuItem('Clientes', 'Clientes con saldo a favor');
$I->checkMenuItem('Clientes', 'Pagos');
$I->checkMenuItem('Clientes', 'Perfiles adicionales');
$I->checkMenuItem('Clientes', 'Tipos de Doc.');
$I->checkMenuItem('Clientes', 'Condiciones frente a IVA');
if (Yii::$app->params['class_customer_required']) {
    $I->checkMenuItem('Clientes', 'Categorías de Cliente');
}
if (Yii::$app->params['category_customer_required']) {
    $I->checkMenuItem('Clientes', 'Rubros de Cliente');
}
$I->checkMenuItem('Clientes', 'Zonas');
$I->checkMenuItem('Clientes', 'Descuentos');
//$I->checkMenuItem('Clientes', 'Facturado y Cobrado');
