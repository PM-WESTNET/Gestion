<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:42
 */

namespace app\modules\westnet\mesa\components\request;


use app\modules\westnet\mesa\components\models\Categoria;
use app\modules\westnet\sequre\components\RestRequest;

class CategoriaRequest extends RestRequest
{

    const BASE_URL = 'gestion/categorias';

    public function __construct($base_url)
    {
        parent::__construct($base_url);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * @param array $params
     */
    public function findAll($params = [], $as_objects=true)
    {
        $response = parent::getRequest(CategoriaRequest::BASE_URL, RestRequest::METHOD_POST, []);
        if($response['code'] == 200 || $response['code'] == 201) {
            $result = [];
            $data = $response['response'];;

            if($as_objects) {
                foreach ($data as $cat) {
                    $result[] = new Categoria($cat);
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
        foreach ($data as $cat) {
            if($cat['id'] == $id) {
                return new Categoria($cat);
            }
        }
        return null;
    }
}