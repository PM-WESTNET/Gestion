<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that comprobantes menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Comprobantes', 'Resumen de comprobantes');
$I->checkMenuItem('Comprobantes', 'Mis comprobantes', '', false);
$I->checkMenuItem('Comprobantes', 'Todos los comprobantes', '', false);
$I->checkMenuItem('Comprobantes', 'IVA Ventas');
$I->checkMenuItem('Comprobantes', 'IVA Compras');
$I->checkMenuItem('Comprobantes', 'Productos para IIBB');

$I->checkMenuItem('Comprobantes', 'Todos los comprobantes', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Facturas A', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Facturas B', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Facturas C', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Presupuestos', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Crédito A', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Crédito B', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Crédito C', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Débito A', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Débito B', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Notas Débito C', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Remitos', '', false);
$I->checkMenuItem('Comprobantes', 'Lista: Pedidos', '', false);
$I->checkMenuItem('Comprobantes', 'Todas las facturas', '', false);

$I->checkMenuItem('Comprobantes', 'Configuración', 'Tipos de comprobante');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Unidades');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Monedas');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Condiciones frente a IVA');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Medios de pago');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Impuestos');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Tasas impositivas');
$I->checkMenuItem('Comprobantes', 'Configuración', 'Clases Facturación E.');
