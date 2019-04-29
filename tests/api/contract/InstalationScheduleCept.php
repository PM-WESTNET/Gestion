<?php

use yii\helpers\Url;

$random = rand(111, 999);

$I = new ApiGuy($scenario);
$I->wantTo('get instalation schedule is availabe via API');

$I->amHttpAuthenticated('superadmin', 'superadmin');

$contract = \app\modules\sale\modules\contract\models\Contract::findOne(['contract_id' => 1]);
$contract->instalation_schedule = 'in the morning';
$save = $contract->save();

assert($save);

$I->sendGET(Url::to(['/westnet/api/contract/set-tentative-node', 'id' => 1, 'node' => $random]));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains('in the morning');

// Update it

$contract = \app\modules\sale\modules\contract\models\Contract::findOne(['contract_id' => 1]);
$contract->instalation_schedule = 'in the afternoon';
$save = $contract->save();

assert($save);

$I->sendGET(Url::to(['/westnet/api/contract/set-tentative-node', 'id' => 1, 'node' => $random]));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains('in the afternoon');
