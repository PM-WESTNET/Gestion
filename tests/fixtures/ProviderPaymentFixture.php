<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ProviderPaymentFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\provider\models\ProviderPayment';

    public $depends = [
        ProviderFixture::class,
        CompanyFixture::class,
        PartnerDistributionModelFixture::class
    ];
}