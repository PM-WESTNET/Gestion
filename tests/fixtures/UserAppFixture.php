<?php

namespace app\tests\fixtures;

use app\modules\mobileapp\v1\models\UserApp;
use yii\test\ActiveFixture;

class UserAppFixture extends ActiveFixture
{
    public $modelClass = UserApp::class;

    public $depends = [
    ];
}