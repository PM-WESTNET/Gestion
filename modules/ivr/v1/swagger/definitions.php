<?php

namespace app\models\v1\swagger;

/**
 * @SWG\Definition(
 *      definition="Error",
 *      required={"code", "message"},
 *      @SWG\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=401
 *      ),
 *      @SWG\Property(
 *          property="message",
 *          type="string",
 *          example="You are requesting with an invalid credential."
 *      )
 * ),
 * @SWG\Definition(
 *      definition="Error1",
 *      required={"error"},
 *      @SWG\Property(
 *          property="error",
 *          type="string",
 *          format="string",
 *          example= "Mensaje de Error"
 *      )
 * ),
 * @SWG\Definition(
 *      definition="Error2",
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          format="string",
 *          example= "Unauthorized"
 *      ),
 *     @SWG\Property(
 *          property="message",
 *          type="string",
 *          format="string",
 *          example= "The access token provided is invalid."
 *      ),
 *     @SWG\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example= 0
 *      ),
 *     @SWG\Property(
 *          property="status",
 *          type="integer",
 *          format="int32",
 *          example= 401
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          type="string",
 *          format="string",
 *          example= "yii\\web\\UnauthorizedHttpException"
 *      ),
 *
 * ),
 * @SWG\Definition(
 *      definition="PaymentMethod",
 *      @SWG\Property(
 *          property="payment_method_id",
 *          type="integer",
 *          format="int32",
 *          example= "1"
 *      ),
 *     @SWG\Property(
 *          property="name",
 *          type="string",
 *          format="string",
 *          example= "Pago Facil"
 *      ),
 *     @SWG\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example= "enabled"
 *      )
 * )
 */

/**
 * @SWG\Definition(required={"id"}, @SWG\Xml(name="Id"))
 */
class Id
{
    /**
     * ??????ID
     *
     * @SWG\Property(example = 10000)
     *
     * @var integer
     */
    public $id;
}

/**
 * @SWG\Definition(required={"access_token", "expires_in", "token_type", "scope", "refresh_token"}, @SWG\Xml(name="AccessToken"))
 */
class AccessToken
{
    /**
     * Access Token
     *
     * @SWG\Property()
     *
     * @var string
     */
    public $access_token;
    /**
     * Vencimiento del token
     * @SWG\Property()
     *
     * @var string
     */
    public $expires_in;

    /**
     * Tipo del token: Bearer
     * @SWG\Property()
     *
     * @var string
     */
    public $token_type;
    /**
     * Scope
     * @SWG\Property()
     *
     * @var string
     */
    public $scope;

    /**
     * Token de refresh
     * @SWG\Property()
     *
     * @var string
     */
    public $refresh;
}