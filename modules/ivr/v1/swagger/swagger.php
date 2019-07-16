<?php

namespace api\modules\v1\swagger;

/**
 * @SWG\Swagger(
 *     schemes={"https"},
 *     host="gestion.westnet.com.ar",
 *     basePath="/index.php?r=ivr/v1",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Westnet IVR",
 *         description="Version: __1.0.0__",
 *         @SWG\Contact(name = "Quoma", email = "info@quoma.com.ar")
 *     ),
 * )
 *
 * @SWG\Tag(
 *   name="user",
 *   description="用户相关操作",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about our store",
 *     url="http://swagger.io"
 *   )
 * )
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
