<?php

/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:53
 */

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\sale\models\TaxRate;

class TaxRateFixture extends ActiveFixture
{

    public $modelClass = TaxRate::class;

    public $depends = [
    ];
}