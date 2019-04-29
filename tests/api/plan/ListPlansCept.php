<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get plans via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');

$I->sendGET(Url::to(['/sale/api/plan/index']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();

$I->dontSeeResponseContains('Manzana');
$I->dontSeeResponseContains('Kiwi');
$I->seeResponseContains('Gold');
$I->seeResponseContains('Bronce');
