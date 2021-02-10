<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\westnet\models\IpAddress;

class IpAddressFixture extends ActiveFixture
{
    public $modelClass = IpAddress::class;
    public $depends = [
        IpRangeFixture::class
    ];
}