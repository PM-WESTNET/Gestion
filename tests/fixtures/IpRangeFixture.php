<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\westnet\models\IpRange;

class IpRangeFixture extends ActiveFixture 
{
    public $modelClass = IpRange::class;
    public $depends = [
        NodeFixture::class
    ];
}