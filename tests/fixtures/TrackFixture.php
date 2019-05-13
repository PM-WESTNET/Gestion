<?php

namespace app\tests\fixtures;

use app\modules\checkout\models\Track;
use yii\test\ActiveFixture;

class TrackFixture extends ActiveFixture
{

    public $modelClass = Track::class;

    public $depends = [
    ];
}