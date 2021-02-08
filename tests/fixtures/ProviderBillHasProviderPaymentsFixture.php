<?php

namespace app\tests\fixtures;

use app\modules\provider\models\ProviderBillHasProviderPayment;
use app\modules\provider\models\ProviderPayment;
use yii\test\ActiveFixture;

class ProviderBillHasProviderPaymentsFixture extends ActiveFixture
{

    public $modelClass = ProviderBillHasProviderPayment::class;

    public $depends = [
        ProviderPayment::class
    ];
}