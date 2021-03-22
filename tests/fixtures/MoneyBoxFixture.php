<?php

namespace app\tests\fixtures;

use app\modules\accounting\models\MoneyBox;
use yii\test\ActiveFixture;
use app\tests\fixtures\MoneyBoxTypeFixture;

class MoneyBoxFixture extends ActiveFixture
{

    public $modelClass = MoneyBox::class;

    public $depends = [
        MoneyBoxTypeFixture::class
    ];
}