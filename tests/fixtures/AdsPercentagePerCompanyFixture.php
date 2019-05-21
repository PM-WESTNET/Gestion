<?php

namespace app\tests\fixtures;

use app\modules\westnet\models\AdsPercentagePerCompany;
use yii\test\ActiveFixture;

class AdsPercentagePerCompanyFixture extends ActiveFixture
{

    public $modelClass = AdsPercentagePerCompany::class;

    public $depends = [
        CompanyFixture::class,
    ];
}