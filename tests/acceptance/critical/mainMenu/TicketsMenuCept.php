<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that ticket menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Tickets', 'Tickets');
$I->checkMenuItem('Tickets', 'Alta Ticket');
$I->checkMenuItem('Tickets', 'Clientes con tickets abiertos', '', false);
$I->checkMenuItem('Tickets', 'Categorías de ticket');
$I->checkMenuItem('Tickets', 'Estados de ticket');
$I->checkMenuItem('Tickets', 'Colores');
