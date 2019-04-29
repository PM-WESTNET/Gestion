<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);

// Navego la página

$I->loginAsSuperadmin();

$I->see('Westnet');
