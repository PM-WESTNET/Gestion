<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 16/07/19
 * Time: 11:55
 */

namespace app\tests\fixtures;


use conquer\oauth2\models\AccessToken;
use yii\test\ActiveFixture;

class Oauth2AccessToken extends ActiveFixture
{
    public $modelClass = AccessToken::class;

    public $depends= [
        UserFixture::class
    ];
}