<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 15/07/19
 * Time: 11:59
 */

namespace app\modules\ivr\v1\models;


use conquer\oauth2\OAuth2IdentityInterface;
use yii\web\IdentityInterface;

class User extends \webvimark\modules\UserManagement\models\User implements OAuth2IdentityInterface
{

    /**
     * Find idenity by username
     * @param string $username current username
     * @return IdentityInterface
     */
    public static function findIdentityByUsername($username)
    {
       return self::findOne(['username' => $username]);
    }


}