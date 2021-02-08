<?php

namespace app\tests\fixtures;

use app\modules\mobileapp\v1\models\MobilePush;
use yii\test\ActiveFixture;

class MobilePushFixture extends ActiveFixture
{
    public $modelClass = MobilePush::class;

    public $depends = [
    ];
}