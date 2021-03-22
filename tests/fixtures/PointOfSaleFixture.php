<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\PointOfSale;
use app\tests\fixtures\CompanyFixture;

class PointOfSaleFixture extends ActiveFixture
{

    public $modelClass = PointOfSale::class;

    public $depends = [
        CompanyFixture::class
    ];
}