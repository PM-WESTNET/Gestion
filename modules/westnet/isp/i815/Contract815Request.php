<?php

namespace app\modules\westnet\isp\i815;

use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\isp\i815\Client815Request;
use app\modules\westnet\isp\models\Contract;
use app\modules\westnet\sequre\components\RestRequest;
use Yii;

/**
 * Description of ClientRequest
 *
 * @author cgarcia
 */
class Contract815Request implements ApiInterface
{

    private $base_url  = '';
    private $internal_url = 'integracion/clientes/cuentasimple/';
    private $token;

    private $_nodes = [];
    private $_services = [];

    const Q_EXTERNAL_ID = 0;
    const Q_CLIENT_ID = 1;
    const Q_ID = 2;
    const Q_IP = 3;

    public function __construct($base_url, $token='')
    {
        $this->base_url = $base_url;
        $this->token = $token;
    }

    /**
     * Creates a new contract. Sequre validations:
     *
     *      validates_presence_of :ip, :ceil_dfl_percent, :client, :plan
     *      validates_uniqueness_of :ip
     *
     * @param Contract $contract
     * @return Contract|string
     * @throws \yii\web\HttpException
     */
    public function create($contract)
    {
        if(((int)$contract->external_id) != 0) {
            // Verifico que el contrato no exista, esto lo hago buscando por el id externo
            $contractOrig = $this->find($contract->external_id, Contract815Request::Q_EXTERNAL_ID);
            if($contractOrig) {
                return $this->update($contract);
            }
        }

        $ip_id = $this->findIpId($contract->ip);

        // Traigo el customer para poder sacar los datos
        $customer = Customer::findOne(['customer_id'=> $contract->client_id_original]);

        $client_id = $this->findClient($customer->code);


        $nodo_pk = NodeService::getNode($this->base_url, $this->token, $contract->node);
        $servicio_id = NodeService::getService($this->base_url, $this->token, $nodo_pk);
        //Creates a new request for contract creation
        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'crear/', [
            'cliente' => $client_id,
            'conector' => $contract->external_id,
            'nombre' => $customer->code . " - " . $customer->getFullName(),
            'email' => $customer->email,
            'telefono' => $customer->phone,
            'fecha_de_alta' => $contract->created_at,
            'activa' => ($contract->state=='enabled' ? 1 : 0 ),
            'ciudad' => Config::getValue('815_ciudad_id'),
            'domicilio' => substr($customer->address->getFullAddress(), 0,300),
            'direccion_ip' => $ip_id,
            'plan' => (string)$contract->plan_id,
            'fecha_de_alta' => (new \DateTime($contract->created_at))->format('Y-m-d'),
            'modo_de_conexion' => 'ipest',
            'acceso_ip_estatica'=> $servicio_id,
            'nodo_de_red' => $nodo_pk
        ]);
        if($response) {
            $contract->id = (string)$response->object['pk'];
            return $contract;
        } else {
            return null;
        }
    }

    private function findIpId($ip)
    {
        // Busco el id de la IP
        $ipResponse = (new CurlXml( $this->base_url, $this->token))->request('integracion/red/direccionip/listar/', [
            'direccion_ip' => $ip,
        ]);
        return (string)$ipResponse->object['pk'];
    }

    private function findIp($ip)
    {
        // Busco el id de la IP
        $ipResponse = (new CurlXml( $this->base_url, $this->token))->request('integracion/red/direccionip/listar/', [
            'pk' => $ip,
        ]);
        return (string)$ipResponse->field[1];
    }

    private function findClient($id)
    {
        $request = new Client815Request($this->base_url, $this->token);
        $response = $request->find($id, Client815Request::Q_EXT_ID);
        return (string)$response[0]->id;
    }

    /**
     * Retorno todos los contratos de un servidor
     * @return array|bool
     */
    public function listAll() {
        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/');

        if($response) {
            $contracts = [];
            foreach ($response->object as $obj) {

                $attribs = [];
                $attribs['id']               = (string)$obj['pk'];
                $attribs['external_id']      = (string)$obj->field[20];
                $attribs['plan_id']          = (string)$obj->field[7];;
                $attribs['client_id']        = (string)$obj->field[1];
                $attribs['ip']               = $this->findIp((string)$obj->field[11]);
                $attribs['node']             = '';
                $contracts[] = new Contract($attribs);
            }
            return $contracts;
        }

        return false;
    }

    /**
     * Finds a particular contract
     *
     * @param integer $id
     * @param integer $type
     * @return array|bool
     * @throws \yii\web\HttpException
     */
    public function find($value = 0, $type = Contract815Request::Q_EXTERNAL_ID )
    {
        if($type==Contract815Request::Q_EXTERNAL_ID) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['conector'=>$value]);
        } elseif($type==Contract815Request::Q_ID) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['pk'=>$value]);
        } elseif($type==Contract815Request::Q_IP) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['direccion_ip__direccion_ip'=>$value]);
        } else {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['cliente'=>$value]);
        }

        if($response) {
            $contract = [];
            // Siempre trae un array con otro array llamado client
            foreach($response->object as $obj ) {
                $attribs = [];
                $attribs['id']              = (string)$obj['pk'];
                $attribs['external_id']     = (string)$obj->field[20];
                $attribs['plan_id']         = (string)$obj->field[7];
                $attribs['client_id']       = (string)$obj->field[1];
                $attribs['ip']              = (string)$obj->field[19];
                $attribs['node']            = (string)$obj->field[9];

                $contract[] = new Contract($attribs);
            }
            return $contract;
        }
        return false;
    }

    /**
     * Updates a particular contract
     *
     * @param Contract $contract
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function update($contract)
    {
        // Busco el contrato
        $contractOrig = $this->find($contract->external_id, Contract815Request::Q_EXTERNAL_ID);

        if(!$contractOrig) {
            return false;
        }

        $contractOrig = $contractOrig[0];
        $contractOrig->merge($contract);

        $contractOrig->updated_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $ip_id = $this->findIpId($contract->ip);

        // Traigo el customer para poder sacar los datos
        $customer = Customer::findOne(['customer_id'=> $contract->client_id_original]);

        $client_id = $this->findClient($customer->code);
        $nodo_pk = NodeService::getNode($this->base_url, $this->token, $contract->node);
        $servicio_id = NodeService::getService($this->base_url, $this->token, $nodo_pk);
        //Creates a new request for contract creation
        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'modificar/', [
            'pk' => $contractOrig->id,
            'cliente' => $client_id,
            'conector' => $contract->external_id,
            'nombre' => $customer->code . " - " . $customer->getFullName(),
            'email' => $customer->email,
            'telefono' => $customer->phone,
            'fecha_de_alta' => $contract->created_at,
            'activa' => ($contract->state=='enabled' ?1 : 0 ),
            'ciudad' => Config::getValue('815_ciudad_id'),
            'domicilio' => substr($customer->address->getFullAddress(),0,300),
            'direccion_ip' => $ip_id,
            'plan' =>(string)$contract->plan_id,
            'fecha_de_alta' => (new \DateTime($contract->created_at))->format('Y-m-d'),
            'modo_de_conexion' => 'ipest',
            'acceso_ip_estatica'=> $servicio_id,
            'nodo_de_red' => $nodo_pk
        ]);
        if($response) {
            $contract->id = (string)$response->object['pk'];
            return $contract;
        } else {
            return false;
        }
    }

    /**
     * Borra un cliente.
     * @param $client_id
     * @return bool
     */
    public function delete($contract_id)
    {

        if($contract_id) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'eliminar/', ['pk'=>$contract_id]);
            if( ((string)$response->errores->cuentasimple) == 'El objeto de tipo \'cuentasimple\' no existe.') {
                return 3;
            }

            $token = (string)$response->token_confirmacion;
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'confirmar_eliminar/', ['pk'=>$contract_id, 'token_confirmacion'=>$token]);

            if(isset($response->exito)) {
                return true;
            }
        }
        return false;
    }

}