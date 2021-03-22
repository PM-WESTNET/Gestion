<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('require to be authenticated via API');
$I->sendGET(Url::to(['/sale/api/bill/types']));
$I->seeResponseContains('Unauthorized');

$I->amHttpAuthenticated('superadmin', 'superadmin');

$I->sendGET(Url::to(['/sale/api/bill/types']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->dontSeeResponseContains('Unauthorized');
