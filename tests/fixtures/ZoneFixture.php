<?php

namespace app\tests\fixtures;

use app\modules\zone\models\Zone;
use yii\test\ActiveFixture;

class ZoneFixture extends ActiveFixture
{

    public $modelClass = Zone::class;

    public $depends = [
    ];
}