<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\westnet\ecopagos\models\Cashier;

class CashierFixture extends ActiveFixture
{

    public $modelClass = Cashier::class;

    public $depends = [
        EcopagoFixture::class,
        UserFixture::class
    ];
}

