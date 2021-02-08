<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:43
 */

namespace app\modules\westnet\mesa\components\request;


use app\modules\westnet\mesa\components\models\Ticket;
use app\modules\westnet\sequre\components\RestRequest;
use Yii;

class TicketRequest  extends RestRequest
{

    public $error;
    const BASE_URL = 'gestion/';

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
    public function create($autor_id, $asignado_id, $categoria_id, $descripcion, $contrato_id, $customer_code, $external_tag_id)
    {
        $result = $this->rest(TicketRequest::BASE_URL . "ticket_nuevo",  [
            'autor_id'      => $autor_id,
            'asignado_id'   => $asignado_id,
            'categoria_id'  => $categoria_id,
            'descripcion'   => $descripcion,
            'customer_code'   => $customer_code,
            'contract_id'   => $contrato_id,
            'etiqueta' => $external_tag_id
        ], RestRequest::METHOD_POST, false, true);
        if(is_array($result) && array_key_exists('error', $result))  {
            $this->error = Yii::t('westnet', 'Can\'t {action} a ticket. Error: {error}.', [
                'action'=> Yii::t('westnet', 'create') ,
                'error' => $result['error']]);
            return null;
        } else {
            return $result['ticket_id'];
        }
    }

    /**
     * Busca un ticket por ID
     * @param $id
     * @param bool $as_object
     * @return Ticket|null
     */
    public function findById($id, $as_object=true)
    {
        $data = $this->rest(TicketRequest::BASE_URL . "ticket/".$id, [], RestRequest::METHOD_GET);
        $ticket = null;
        if($data) {
            if($as_object) {
                $ticket = new Ticket($id, $data);
            } else {
                $ticket = $data;
            }
        } else {
            $this->error = Yii::t('westnet', 'Ticket not found.');
        }
        return $ticket;
    }

    /**
     * Actualiza el estado y descripcion de un ticket
     * @param $id
     * @param $estado
     * @param $descripcion
     * @return bool|null
     */
    public function update($id, $estado, $descripcion, $autor_id, $dateTime=null)
    {
        $result = $this->rest(TicketRequest::BASE_URL . "actualizar_ticket/".$id, [
            'estado'        => $estado,
            'descripcion'   => $descripcion,
            'autor_id'      => $autor_id,
            'fecha'         => $dateTime
        ], RestRequest::METHOD_POST, false, true);

        if(is_array($result) && array_key_exists('error', $result))  {
            $this->error = Yii::t('westnet', 'Can\'t {action} a ticket. Error: {error}', [
                'action'=> Yii::t('westnet', 'update') ,
                'error' => $result['error']]);
            return null;
        } else {
            return true;
        }
    }


    /**
     * Retorna los estados de tickets
     * @return null
     */
    public function findEstados()
    {
        return $this->rest(TicketRequest::BASE_URL . "estados_ticket/", [], RestRequest::METHOD_GET);
    }
}