<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\PointOfSale;

class PointOfSaleFixture extends ActiveFixture
{

    public $modelClass = PointOfSale::class;

    public $depends = [
    ];
}