<?php

namespace app\tests\fixtures;

use app\modules\sale\models\Currency;
use yii\test\ActiveFixture;

class CurrencyFixture extends ActiveFixture
{

    public $modelClass = Currency::class;

    public $depends = [
    ];
}