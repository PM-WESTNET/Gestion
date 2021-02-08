<?php

namespace app\tests\fixtures;

use app\modules\checkout\models\PaymentMethod;
use yii\test\ActiveFixture;

class PaymentMethodFixture extends ActiveFixture
{

    public $modelClass = PaymentMethod::class;

    public $depends = [
    ];
}