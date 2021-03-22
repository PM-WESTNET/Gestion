<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get companies via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');

$I->sendGET(Url::to(['/sale/api/company/index']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();

$I->seeResponseContains('ACME');
$I->seeResponseContains('Metro');
