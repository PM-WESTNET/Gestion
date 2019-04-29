<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\accounting\models\AccountingPeriod;

class AccountingPeriodFixture extends ActiveFixture
{

    public $modelClass = AccountingPeriod::class;

    public $depends = [
    ];
}