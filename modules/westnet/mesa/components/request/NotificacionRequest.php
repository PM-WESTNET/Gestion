<?php

namespace app\modules\westnet\mesa\components\request;

use app\modules\westnet\mesa\components\models\Ticket;
use app\modules\westnet\sequre\components\RestRequest;
use Yii;

/**
 * Description of Notificacion
 *
 * @author mmoyano
 */
class NotificacionRequest  extends RestRequest
{

    public $error;
    const BASE_URL = '/gestion/';

    public function __construct($base_url)
    {
        parent::__construct($base_url);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    private function rest($url, $params = [], $method = RestRequest::METHOD_POST, $rawBody=false, $postJson=false)
    {
        $response = parent::getRequest($url, $method, $params, $rawBody, $postJson);
        if($response['code'] == 200 || $response['code'] == 201) {
            return $response['response'];
        } else {
            return null;
        }
    }

    /**
     * Funcion para crear un ticket en el sistema de westnet
     * @param $autor_id
     * @param $asignado_id
     * @param $categoria_id
     * @param $descripcion
     * @param $contrato_id
     * @return null or ticket_id
     */
    public function create($notificacion)
    {
        $result = $this->rest(self::BASE_URL . "notificacion", (array)$notificacion, RestRequest::METHOD_POST, false, true);
        
        if(is_array($result) && array_key_exists('error', $result))  {
            $this->error = Yii::t('westnet', 'Can\'t {action} a ticket. Error: {error}.', [
                'action'=> Yii::t('westnet', 'create') ,
                'error' => $result['error']]);
            return null;
        } else {
            return true;
        }
    }
    
    /**
     * Actualiza el estado y descripcion de un ticket. Se utiliza el mismo
     * servicio que para alta. Si tiene la misma ip, pisa los valores anteriores.
     * @return bool|null
     */
    public function update($notificacion)
    {
        return $this->create($notificacion);
    }
}