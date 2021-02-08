<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ProviderBillFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\provider\models\ProviderBill';

    public $depends = [
        ProviderFixture::class,
        CompanyFixture::class,
        PartnerDistributionModelFixture::class
    ];
}