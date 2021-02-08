<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\westnet\ecopagos\models\DailyClosure;

class DailyClosureFixture extends ActiveFixture
{

    public $modelClass = DailyClosure::class

    public $depends = [
        CashierFixture::class,
        EcopagoFixture::class,
    ];
}

