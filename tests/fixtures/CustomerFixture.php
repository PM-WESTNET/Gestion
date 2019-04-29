<?php

namespace app\tests\fixtures;

use app\modules\sale\models\Customer;

use yii\test\ActiveFixture;

class CustomerFixture extends ActiveFixture
{

    public $modelClass = Customer::class;

    public $depends = [
        DocumentTypeFixture::class,
        TaxConditionFixture::class,
        AddressFixture::class,
        CompanyFixture::class
    ];
}