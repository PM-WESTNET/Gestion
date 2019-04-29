<?php

namespace app\tests\fixtures;

use app\modules\accounting\models\MoneyBoxType;
use yii\test\ActiveFixture;

class MoneyBoxTypeFixture extends ActiveFixture
{

    public $modelClass = MoneyBoxType::class;

    public $depends = [
        MoneyBoxType::class
    ];
}