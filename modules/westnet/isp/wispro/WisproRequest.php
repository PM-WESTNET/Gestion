<?php

namespace app\modules\westnet\isp\wispro;

use app\modules\westnet\isp\ApiProvider;
use app\modules\westnet\sequre\components\RestRequest;

/**
 * Description of PlansRequest
 *
 * @author cgarcia
 */
class WisproRequest extends RestRequest implements ApiProvider
{

    const BASE_URL = 'api/apply_changes';

    public function __construct($base_url, $token='')
    {
        parent::__construct($base_url, $token);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * Aplica cambios en el servidor Wispro
     * @return bool
     */
    public function apply()
    {
        //Creates a new request for a client view
        $response = parent::getRequest(WisproRequest::BASE_URL, RestRequest::METHOD_GET);

        if($response['code'] == 200 ) {
            return true;
        } else {
            return false;
        }
    }
}