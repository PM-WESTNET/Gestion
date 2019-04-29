<?php

namespace app\tests\fixtures;

use app\modules\accounting\models\MoneyBox;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\sale\models\Currency;
use yii\test\ActiveFixture;

class MoneyBoxAccountFixture extends ActiveFixture
{

    public $modelClass = MoneyBoxAccount::class;

   /* public $depends = [
        MoneyBox::class,
        Currency::class,
        CompanyFixture::class,
    ];*/
}