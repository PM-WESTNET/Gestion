<?php

use yii\helpers\Url;

$I = new ApiGuy($scenario);
$I->wantTo('get bill types via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');
$I->sendGET(Url::to(['/sale/api/bill/types']));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->dontSeeResponseContains('error');

$types = app\modules\sale\models\BillType::find()->all();

foreach ($types as $type) {
    $I->seeResponseContains($type->name);
}
