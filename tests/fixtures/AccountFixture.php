<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class AccountFixture extends ActiveFixture
{

    public $modelClass = 'app\modules\accounting\models\Account';

    public $depends = [
    ];
}