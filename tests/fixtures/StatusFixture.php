<?php

namespace app\tests\fixtures;

use app\modules\westnet\ecopagos\models\Status;
use yii\test\ActiveFixture;

class StatusFixture extends ActiveFixture
{

    public $modelClass = Status::class;

    public $depends = [
    ];

    public $db = 'dbecopago';
}