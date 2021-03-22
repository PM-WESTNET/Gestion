<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;
use app\modules\accounting\models\AccountConfig;

class AccountConfigFixture extends ActiveFixture
{

    public $modelClass = AccountConfig::class;

    public $depends = [
    ];
}