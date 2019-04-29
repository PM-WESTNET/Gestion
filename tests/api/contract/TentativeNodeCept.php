<?php

use yii\helpers\Url;

$random = rand(111, 999);

$contract = \app\modules\sale\modules\contract\models\Contract::findOne(['contract_id' => 1]);

$original = $contract->tentative_node;

$I = new ApiGuy($scenario);
$I->wantTo('get bill types via API');
$I->amHttpAuthenticated('superadmin', 'superadmin');
$I->sendPOST(Url::to(['/westnet/api/contract/set-tentative-node']), ['id' => 1, 'node' => $random]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->dontSeeResponseContains('error');

$contract = \app\modules\sale\modules\contract\models\Contract::findOne(['contract_id' => 1]);

assert($contract->tentative_node != $original);
assert($contract->tentative_node == $random);

$I->sendPOST(Url::to(['/westnet/api/contract/list-by-id']), ['id' => 1]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->dontSeeResponseContains('error');

$I->seeResponseContains((string)$random);
