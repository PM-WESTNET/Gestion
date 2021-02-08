<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CompanyFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\sale\models\Company';

    public $depends = [
        TaxConditionFixture::class,
    ];
}