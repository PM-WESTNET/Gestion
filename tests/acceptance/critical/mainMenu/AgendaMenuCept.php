<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that agenda menu works in every item');

 $I->loginAsSuperadmin();

$I->checkMenuItem('Agenda', 'Mi agenda');
$I->checkMenuItem('Agenda', 'Tareas');
$I->checkMenuItem('Agenda', 'Categorías de Tarea');
$I->checkMenuItem('Agenda', 'Tipos de Tarea');
$I->checkMenuItem('Agenda', 'Estados de Tarea');
$I->checkMenuItem('Agenda', 'Tipos de Evento');
