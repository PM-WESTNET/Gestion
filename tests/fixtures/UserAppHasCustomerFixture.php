<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 01/07/19
 * Time: 13:39
 */

namespace app\tests\fixtures;


use app\modules\mobileapp\v1\models\UserAppHasCustomer;
use yii\test\ActiveFixture;
use yii\test\Fixture;

class UserAppHasCustomerFixture extends ActiveFixture
{

    public $modelClass = UserAppHasCustomer::class;
    public $depends = [
        UserAppFixture::class,
        CustomerFixture::class
    ];
}