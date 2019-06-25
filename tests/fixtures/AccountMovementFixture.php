<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\accounting\models\AccountMovement;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\AccountingPeriodFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;


class AccountMovementFixture extends ActiveFixture
{

    public $modelClass = AccountMovement::class;

    public $depends = [
        CompanyFixture::class,
        AccountingPeriodFixture::class,
        PartnerDistributionModelFixture::class
    ];
}