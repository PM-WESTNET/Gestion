<?php

namespace app\tests\fixtures;

use app\modules\westnet\ecopagos\models\Collector;
use yii\test\ActiveFixture;

class CollectorFixture extends ActiveFixture
{

    public $modelClass = Collector::class;

    public $depends = [
    ];

    public $db = 'dbecopago';
}