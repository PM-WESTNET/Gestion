<?php

namespace app\modules\westnet\isp\wispro;

use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\sequre\components\RestRequest;

/**
 * Description of PlansRequest
 *
 * @author cgarcia
 */
class PlansRequest extends RestRequest implements ApiInterface
{

    const BASE_URL = 'api/plans';

    public function __construct($base_url, $token='')
    {
        parent::__construct($base_url, $token);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }
    
    public function listAll()
    {
        //Creates a new request for a client view
        $response = parent::getRequest(PlansRequest::BASE_URL, RestRequest::METHOD_GET);

        if($response['code'] == 200 ) {
            return $response['response'];
        } else {
            return false;
        }
    }

    /**
     * Retorna un plan del servidor, buscando por nombre.
     * @param $name
     * @return mixed
     */
    public function find($name, $type = 0)
    {
        $planes = $this->listAll();

        if($planes) {
            foreach($planes as $plan) {
                if( trim(str_replace("\n","", preg_replace("[ |/]", "-", strtolower($plan['plan']['name'])))) == trim($name) ) {
                    return $plan['plan'];
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Crea un objeto en el servidor.
     *
     * @param $object
     * @return mixed
     */
    public function create($object)
    {
        // TODO: Implement create() method.
    }

    /**
     * Actualiza un objeto en el servidor.
     *
     * @param $object
     * @return mixed
     */
    public function update($object)
    {
        // TODO: Implement update() method.
    }

    /**
     * Borra un objeto en el servidor
     *
     * @param $object
     * @return mixed
     */
    public function delete($object)
    {
        // TODO: Implement delete() method.
    }

}