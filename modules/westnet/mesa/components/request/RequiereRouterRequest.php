<?php

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:42
 */

namespace app\modules\westnet\mesa\components\request;

use app\modules\westnet\mesa\components\models\Usuario;
use app\modules\westnet\sequre\components\RestRequest;

class RequiereRouterRequest extends RestRequest {

    const BASE_URL = 'gestion/requerir_router';

    public function __construct($base_url) {
        parent::__construct($base_url);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function requiere($ticket, $requiere)
    {
        return parent::getRequest(RequiereRouterRequest::BASE_URL.'/'.$ticket.'/'.($requiere ? 'true' : 'false' ), RestRequest::METHOD_GET, []);
    }

}
