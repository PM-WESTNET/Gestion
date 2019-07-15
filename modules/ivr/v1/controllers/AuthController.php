<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 15/07/19
 * Time: 15:55
 */

namespace app\modules\ivr\v1\controllers;


use app\modules\ivr\v1\components\Controller;
use conquer\oauth2\TokenAction;

class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            /**
             * Checks oauth2 credentions and try to perform OAuth2 authorization on logged user.
             * AuthorizeFilter uses session to store incoming oauth2 request, so
             * you can do additional steps, such as third party oauth authorization (Facebook, Google ...)
             */
            'oauth2Auth' => [
                'class' => \conquer\oauth2\AuthorizeFilter::class,
                'only' => ['index'],
            ],
        ];
    }

    public function actions()
    {
        return [
            // returns access token
            'token' => [
                'class' => TokenAction::class,
                'grantTypes' => [
                    'password' => 'conquer\oauth2\granttypes\UserCredentials',
                    'refresh_token' => 'conquer\oauth2\granttypes\RefreshToken',
                ]
            ],
        ];
    }
}