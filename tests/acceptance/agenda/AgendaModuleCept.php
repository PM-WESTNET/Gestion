<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that Agenda accessibility works');

$I->loginAsUser();

if (Yii::$app->params['agenda_enabled']) {
    $I->see('Agenda');
} else {
    $I->dontSee('Agenda');
}
