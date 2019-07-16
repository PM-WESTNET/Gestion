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

    /**
     * @SWG\Post(path="/auth/token",
     *     tags={"Auth"},
     *     summary="",
     *     description="Devuelve el token de acceso a la api.
     *      Se debe de enviar en el cuerpo del request:
     *          username: username del usuario de IVR de gestion. Usuario IVR: ivr_user
     *          password: contraseña del usuario de IVR de gestion. Contraseña: aT63A7eYRv8wwAsv
     *          client_id: ivr_user,
     *          client_secret: 4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe
     *
     *     En el resto de endpoints de la api se debe enviar la cabecera Authorization: bearer 'token' y
     *     ademas las cabeceras client_id y client_secret
     *     ",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "username",
     *        description = "Username de gestion",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "password",
     *        description = "Contraseña de gestion",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "client_id",
     *        description = "ID del cliente de OAuth2",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "client_secret",
     *        description = "Secret key de OAuth2",
     *        required = true,
     *        type = "string"
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "Devuelve un array con el token",
     *         @SWG\Schema(ref="#/definitions/AccessToken"),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Error al solicitar token",
     *         @SWG\Schema(ref="#/definitions/Error1")
     *     )
     * )
     *
     */
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