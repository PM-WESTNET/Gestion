<?php

namespace app\tests\fixtures;

use app\modules\cobrodigital\models\PaymentCardFile;
use app\modules\cobrodigital\models\PaymentCard;
use yii\test\ActiveFixture;

class PaymentCardFixture extends ActiveFixture
{
    public $modelClass = PaymentCard::class;

    public $depends = [
        PaymentCardFileFixture::class
    ];

}