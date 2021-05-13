<?php

namespace app\modules\westnet\api\swagger;

/**
 * @SWG\Swagger(
 *     schemes={"https"},
 *     host="gestion_westnet.local:8100",
 *     basePath="/isp/api",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Gestion-Soldef API",
 *         description="Version: __1.0.0__",
 *         @SWG\Contact(name = "IspGestion", email = "@gmail.com.ar")
 *     ),
 *
 * ),
 *
 *
 * @SWG\Tag(
 *   name="Auth",
 *   description="Autenticación en la api",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about our store",
 *     url="http://swagger.io"
 *   )
 *)
 *
 * @SWG\Tag(
 *   name="Contrato",
 *   description="***",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about our store",
 *     url="http://swagger.io"
 *   )
 *)
 */

/**
 * @SWG\Definition(
 *   @SWG\Xml(name="##default")
 * )
 */
class ApiResponse
{
    /**
     * @SWG\Property(format="int32", description = "code of result")
     * @var int
     */
    public $code;
    /**
     * @SWG\Property
     * @var string
     */
    public $type;
    /**
     * @SWG\Property
     * @var string
     */
    public $message;
    /**
     * @SWG\Property(format = "int64", enum = {1, 2})
     * @var integer
     */
    public $status;
}
