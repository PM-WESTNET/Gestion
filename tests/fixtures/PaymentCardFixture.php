<?php

namespace app\tests\fixtures;

use app\modules\cobrodigital\models\PaymentCardFile;
use yii\test\ActiveFixture;

class PaymentCardFixture extends ActiveFixture
{
    public $modelClass = PaymentCardFile::class;

    public $depends = [
        PaymentCardFileFixture::class
    ];

}