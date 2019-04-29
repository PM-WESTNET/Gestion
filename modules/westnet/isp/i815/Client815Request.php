<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/01/17
 * Time: 15:47
 */

namespace app\modules\westnet\isp\i815;

use app\modules\config\models\Config;
use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\sequre\components\RestRequest;


class Client815Request implements ApiInterface
{

    private $base_url  = '';
    private $internal_url = 'integracion/clientes/cliente/';
    private $token;

    const Q_NAME    = 0;
    const Q_ID      = 1;
    CONST Q_EXT_ID  = 2;

    public function __construct($base_url, $token='')
    {
        $this->base_url = $base_url;
        $this->token = $token;
    }

    /**
     * Creates a new client.
     *
     * @throws \yii\web\HttpException
     */
    public function create($client)
    {
        $clientOrig = null;
        // Busco al cliente por el id externo
        if(!empty($client->external_client_number)) {
            $clientOrig = $this->find($client->external_client_number, Client815Request::Q_EXT_ID);
        }

        // Si no lo encuentro por id externo, lo busco por nombre
        // Dejo de buscar por nombre para que si o si me traiga por el externa_client_number (code en customer)
        /*if(!$clientOrig) {
            $clientOrig = $this->find($client->name, Client815Request::Q_NAME);
        }*/
        // Como existe retorno el id del objeto que encontre.
        if ($clientOrig) {
            $client = $clientOrig[0];
            return  $client->id ;
        }

        $client->created_at = (new \DateTime('now'))->format('Y-m-d');

        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'crear/', [
            'conector' => $client->external_client_number,
            'nombre' => $client->external_client_number . " - " . $client->name,
            'email' => $client->email,
            'telefono' => $client->phone,
            'fecha_de_alta' => $client->created_at,
            'activo' => 1,
            'ciudad' => Config::getValue('815_ciudad_id'),
            'domicilio' => substr($client->address,0,300),
        ]);
        if($response) {
            $client->id = (string)$response->object['pk'];
            return $client;
        } else {
            return null;
        }
    }

    /**
     * Retorna un cliente del servidor, buscando por nombre.
     * @param $name
     * @return mixed
     */
    public function find($value, $type=Client815Request::Q_NAME)
    {
        if($type==Client815Request::Q_NAME) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['nombre'=>$value]);
        }
        if($type==Client815Request::Q_ID) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['pk'=>$value]);
        }
        if($type==Client815Request::Q_EXT_ID) {
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/', ['conector'=>$value]);
        }
        if($response) {
            $client = [];
            foreach ($response->object as $obj) {
                $attribs = [];
                $attribs['id'] = (string)$obj['pk'];
                $attribs['name'] = $obj->field[0];
                $attribs['email'] = (string)$obj->field[1];
                $attribs['phone'] = (string)$obj->field[2];
                $attribs['address'] = (string)$obj->field[5];
                $attribs['external_client_number'] = (string)$obj->field[7];

                $client[] = new Client($attribs);
            }

            return $client;
        }

        return false;
    }

    /**
     * Retorna un cliente del servidor, buscando por nombre.
     * @param $name
     * @return mixed
     */
    public function listAll()
    {
        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/');

        if($response) {
            $client = [];
            foreach ($response->object as $obj) {
                $attribs = [];
                $attribs['id'] = (string)$obj['pk'];
                $attribs['name'] = $obj->field[0];
                $attribs['email'] = (string)$obj->field[1];
                $attribs['phone'] = (string)$obj->field[2];
                $attribs['address'] = (string)$obj->field[5];
                $attribs['external_client_number'] = (string)$obj->field[7];

                $client[] = new Client($attribs);
            }
            return $client;
        }

        return false;
    }

    /**
     * Updates a particular client
     * @param integer $clientId
     * @param array $clientData
     * @param string $url
     * @param string $verb
     * @throws \yii\web\HttpException
     */
    public function update($client)
    {
        $clientOrig = null;
        // Busco al cliente por el id externo
        if(!empty($client->external_client_number)) {
            $clientOrig = $this->find($client->external_client_number, Client815Request::Q_EXT_ID);
        }

        // Si definitivamente no existe, lo creo
        if(!$clientOrig) {
            return $this->create($client);
        }
        $clientOrig = $clientOrig[0];

        $client->id = $clientOrig->id;
        $client->updated_at = (new \DateTime('now'))->format('Y-m-d');

        $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'modificar/', [
            'pk'            => $client->id,
            'conector'      => $client->external_client_number,
            'nombre'        => $client->external_client_number . " - " . $client->name,
            'email'         => $client->email,
            'telefono'      => $client->phone,
            'fecha_de_alta' => $client->created_at,
            'activo'        => 1,
            'ciudad'        => Config::getValue('815_ciudad_id'),
            'domicilio'     => substr($client->address,0,300),
        ]);

        if($response) {
            return (string)$response->object['pk'];
        } else {
            return false;
        }
    }

    /**
     * Borra un cliente.
     * @param $client_id
     * @return bool
     */
    public function delete($client_id)
    {
        if($client_id) {
            $client = $this->find($client_id, Client815Request::Q_EXT_ID);
            if($client) {
                $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'eliminar/', ['pk'=>$client[0]->id]);
                $token = (string)$response->token_confirmacion;
                $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'confirmar_eliminar/', ['pk'=>$client[0]->id, 'token_confirmacion'=>$token]);

                if(isset($response->exito)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }
}
