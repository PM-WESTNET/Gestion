<?php

namespace app\tests\fixtures;

use app\modules\accounting\models\MoneyBox;
use yii\test\ActiveFixture;

class MoneyBoxFixture extends ActiveFixture
{

    public $modelClass = MoneyBox::class;

    public $depends = [
    ];
}