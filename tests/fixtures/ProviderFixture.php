<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ProviderFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\provider\models\Provider';

    public $depends = [
        TaxConditionFixture::class,
        AccountFixture::class
    ];
}