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

class UsuarioRequest extends RestRequest {

    const BASE_URL = 'gestion/usuarios';

    public function __construct($base_url) {
        parent::__construct($base_url);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * @param array $params
     */
    public function findAll($params = [], $as_objects = true) {
        $response = parent::getRequest(UsuarioRequest::BASE_URL, RestRequest::METHOD_POST, []);
        if ($response['code'] == 200 || $response['code'] == 201) {
            $result = [];
            $data = $response['response'];
            ;
            if ($as_objects) {
                foreach ($data as $usuario) {
                    $result[] = new Usuario($usuario);
                }
            } else {
                $result = $data;
            }

            return $result;
        } else {
            return null;
        }
    }

    public function findById($id) {
        $data = $this->findAll([], false);
        if (is_array($data)) {
            foreach ($data as $usuario) {
                if ($usuario['id'] == $id) {
                    return new Usuario($usuario);
                }
            }
        }else{
            if ($data['id'] == $id) {
                    return new Usuario($data);
                }
        }
        return null;
    }

}
