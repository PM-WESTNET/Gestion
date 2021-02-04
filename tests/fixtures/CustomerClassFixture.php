<?php

namespace app\tests\fixtures;

use app\modules\sale\models\CustomerClass;
use yii\test\ActiveFixture;

class CustomerClassFixture extends ActiveFixture
{

    public $modelClass = CustomerClass::class;

    public $depends = [
    ];
}