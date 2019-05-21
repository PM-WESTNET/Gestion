<?php

namespace app\tests\fixtures;

use app\modules\checkout\models\CompanyHasPaymentTrack;
use yii\test\ActiveFixture;

class CompanyHasPaymentTrackFixture extends ActiveFixture
{

    public $modelClass = CompanyHasPaymentTrack::class;

    public $depends = [
        CompanyFixture::class,
        PaymentMethodFixture::class,
        TrackFixture::class
    ];
}