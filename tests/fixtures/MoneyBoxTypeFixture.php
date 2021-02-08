<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\accounting\models\MoneyBoxType;

class MoneyBoxTypeFixture extends ActiveFixture
{

    public $modelClass = MoneyBoxType::class;

    public $depends = [
    ];
}