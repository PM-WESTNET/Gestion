<?php

namespace app\tests\fixtures;

use app\modules\sale\models\CompanyHasBilling;
use yii\test\ActiveFixture;

class CompanyHasBillTypeFixture extends ActiveFixture
{

    public $modelClass = CompanyHasBilling::class;

    public $depends = [
        CompanyFixture::class,
        BillTypeFixture::class
    ];
}