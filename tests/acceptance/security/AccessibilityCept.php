<?php

use tests\_pages\LoginPage;
use webvimark\modules\UserManagement\components\AuthHelper;

$I = new WebGuy($scenario);
$I->wantTo('ensure that login works');

$I = LoginPage::openBy($I);

$I->amGoingTo('try to login with empty credentials');
$I->login('roleless_user', 'roleless_user');

$allRoutes = AuthHelper::getRoutes();

$problems = [];

//foreach ($allRoutes as $route) {
//    $route = str_replace('/*', '', $route);
//
//    if (strpos($route, 'captcha') === false &&
//            strpos($route, 'api') === false &&
//            strpos($route, 'login') === false &&
//            strpos($route, 'logout') === false) {
//        $I->amOnPage('index-test.php?r=' . $route);
//        try {
//            $I->seeElement('.site-error');
//        } catch (Exception $ex) {
//            $problems[] = $route;
//        }
//    }
//}

if (count($problems) > 0) {
    foreach ($problems as $problem) {
        $I->expect($problem . ' to be forbidden.');
    }
}

assert(count($problems) == 0);
