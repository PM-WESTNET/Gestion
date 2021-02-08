<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get bill via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');

$searchModel['customer_id'] = 1;

$I->sendGET(Url::to(['/sale/api/bill/index', 'BillSearch' => $searchModel]));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();

$I->seeResponseContains('Juan');
$I->dontSeeResponseContains('José');
