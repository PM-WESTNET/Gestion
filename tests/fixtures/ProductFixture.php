<?php

namespace app\tests\fixtures;

use app\modules\sale\models\Product;
use yii\test\ActiveFixture;

class ProductFixture extends ActiveFixture
{

    public $modelClass = Product::class;

    public $depends = [
    ];
}