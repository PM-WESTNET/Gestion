<?php

$I = new WebGuy($scenario);
$I->wantTo('ensure that main menu works in every item');

 $I->loginAsSuperadmin();

$I->click('Westnet');
$I->seeInTitle('Westnet');
$I->click("//div[@id='wide-navbar']/ul/li/a[contains(text(), 'Home')]");
$I->seeInTitle('Westnet');
