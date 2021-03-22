<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get products via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');

$I->sendGET(Url::to(['/sale/api/product/index']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();

$I->seeResponseContains('Manzana');
$I->seeResponseContains('Kiwi');
$I->dontSeeResponseContains('Gold');
$I->dontSeeResponseContains('Bronce');
