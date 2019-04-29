<?php

namespace app\tests\fixtures;

use app\modules\sale\models\BillType;
use yii\test\ActiveFixture;
use app\tests\fixtures\InvoiceClassFixture;

class BillTypeFixture extends ActiveFixture
{

    public $modelClass = BillType::class;

    public $depends = [
        InvoiceClassFixture::class
    ];
}