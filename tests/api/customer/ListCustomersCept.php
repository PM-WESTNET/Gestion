<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get customers via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');

$I->sendGET(Url::to(['/sale/api/customer/index']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();

$I->seeResponseContains('Juan');
$I->seeResponseContains('José');
