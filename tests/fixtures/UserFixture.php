<?php
/**
 * Created by PhpStorm.
 * User: quoma
 * Date: 21/02/19
 * Time: 11:11
 */

namespace app\tests\fixtures;


use webvimark\modules\UserManagement\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{

    public $modelClass = User::class;


}