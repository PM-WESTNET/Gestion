<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that contabilidad menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Contabilidad', 'Entidades Monetarias');
$I->checkMenuItem('Contabilidad', 'Cuentas Monetarias');
$I->checkMenuItem('Contabilidad', 'Tipos de Entidad Monetaria');
$I->checkMenuItem('Contabilidad', 'Resúmenes bancarios');
$I->checkMenuItem('Contabilidad', 'Conciliaciones');
$I->checkMenuItem('Contabilidad', 'Tipos de Operaciones');
$I->checkMenuItem('Contabilidad', 'Asiento Manual');
$I->checkMenuItem('Contabilidad', 'Libro Diario');
$I->checkMenuItem('Contabilidad', 'Libro Maestro');
$I->checkMenuItem('Contabilidad', 'Periodos Contables');
$I->checkMenuItem('Contabilidad', 'Plan de Cuentas');
$I->checkMenuItem('Contabilidad', 'Configuraciones de Cuentas');
$I->checkMenuItem('Contabilidad', 'Chequeras');
$I->checkMenuItem('Contabilidad', 'Cheques');
