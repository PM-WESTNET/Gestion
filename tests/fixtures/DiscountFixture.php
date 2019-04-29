<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\Discount;

class DiscountFixture extends ActiveFixture
{

    public $modelClass = Discount::class;

    public $depends = [
    ];
}