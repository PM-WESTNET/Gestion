<?php
namespace app\tests\fixtures;

use app\modules\mobileapp\v1\models\AuthToken;
use app\tests\fixtures\UserAppFixture;
use yii\test\ActiveFixture;

class AuthTokenFixture extends ActiveFixture
{
    public $modelClass = AuthToken::class;

    public $depends = [
        UserAppFixture::class
    ];
}