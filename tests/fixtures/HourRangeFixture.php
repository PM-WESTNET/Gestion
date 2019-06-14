<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\HourRange;

class HourRangeFixture extends ActiveFixture
{

    public $modelClass = HourRange::class;

    public $depends = [
    ];
}