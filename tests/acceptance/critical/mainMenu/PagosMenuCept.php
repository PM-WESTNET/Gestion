<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that pagos menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Pagos', 'Planes de Pago');
$I->checkMenuItem('Pagos', 'Medios de pago');
$I->checkMenuItem('Pagos', 'Archivos de Pago Fácil');