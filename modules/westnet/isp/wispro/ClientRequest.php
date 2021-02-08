<?php

namespace app\modules\westnet\isp\wispro;

use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\sequre\components\RestRequest;

/**
 * Description of ClientRequest
 *
 * @author smaldonado
 */
class ClientRequest extends RestRequest implements ApiInterface
{

    const BASE_URL  = 'api/clients';
    const Q_NAME    = 0;
    const Q_ID      = 1;
    CONST Q_EXT_ID  = 2;

    public function __construct($base_url, $token='')
    {
        parent::__construct($base_url, $token);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * Creates a new client. Sequre validations:
     *
     *      validates_presence_of :name
     *      validates_length_of :name, :in => 3..128
     *      validates_format_of :email, :with => /\A([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})\Z/i, :allow_blank => true
     *      validates_uniqueness_of :name (este lo vamos a sacar)
     *
     * @param array $clientData
     * @param string $url
     * @param string $verb
     * @throws \yii\web\HttpException
     */
    public function create($client)
    {
        // Busco al cliente por el id externo
        if(!empty($client->external_client_number)) {
            $clientOrig = $this->find($client->external_client_number, ClientRequest::Q_EXT_ID);
        }
        // Si no lo encuentro por id externo, lo busco por nombre
        if(!$clientOrig) {
            $clientOrig = $this->find($client->name, ClientRequest::Q_NAME);
        }

        // Como existe retorno el id del objeto que encontre.
        if ($clientOrig) {
            $client = $clientOrig[0];
            return  $client->id ;
        }

        $client->created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        //Creates a new request for client creation
        $response = parent::getRequest(ClientRequest::BASE_URL, RestRequest::METHOD_POST, [
            'client' => [
                'id' => $client->id,
                'external_client_number' => $client->external_client_number,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'phone_mobile' => $client->phone_mobile,
                'address' => $client->address,
            ]
        ], false, true);

        if($response['code'] == 200 || $response['code'] == 201) {
            $client->id = $response['response']['client']['id'];
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
    public function find($value, $type=ClientRequest::Q_NAME)
    {
        if($type==ClientRequest::Q_NAME) {
            $response = parent::getRequest(ClientRequest::BASE_URL, RestRequest::METHOD_GET, ['search'=>['name_eq'=>$value]], false, true);
        }
        if($type==ClientRequest::Q_ID) {
            $response = parent::getRequest(ClientRequest::BASE_URL. '/'.$value, RestRequest::METHOD_GET, null, false, true);
        }
        if($type==ClientRequest::Q_EXT_ID) {
            $response = parent::getRequest(ClientRequest::BASE_URL, RestRequest::METHOD_GET, ['search'=>['external_client_number_eq'=>$value]], false, true);
        }
        if((!empty($response) && !empty($response['response']) ) && ($response['code']==200 || $response['code']==201)) {
            $client = [];
            if( array_key_exists('client', $response['response'])) {
                $client[] = new Client($response['response']['client']);
            } else {
                foreach($response['response'] as $value ) {
                    $client[] = new Client($value['client']);
                }
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
        $response = parent::getRequest(ClientRequest::BASE_URL, RestRequest::METHOD_GET, null, false, true);
        if((!empty($response) && !empty($response['response']) ) && ($response['code']==200 || $response['code']==201)) {
            $client = [];
            // Siempre trae un array con otro array llamado client
            foreach($response['response'] as $key=>$value ) {
                $client[] = new Client($value['client']);
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
        // Busco al cliente por el id externo
        if(!empty($client->external_client_number)) {
            $clientOrig = $this->find($client->external_client_number, ClientRequest::Q_EXT_ID);
        }
        // Si no existe lo busco por nombre
        if(!$clientOrig) {
            $clientOrig = $this->find($client->name, ClientRequest::Q_NAME);
        }
        // Si definitivamente no existe, lo creo
        if(!$clientOrig) {
            return $this->create($client);
        }

        // Pongo el id de wispro
        $clientOrig = $clientOrig[0];

        $client->id = $clientOrig->id;
        $client->updated_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        // El update no devuelve nada
        $response = parent::getRequest(ClientRequest::BASE_URL. '/'.$client->id, RestRequest::METHOD_PUT, [
            'client' => [
                'id' => $client->id,
                'external_client_number' => $client->external_client_number,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'phone_mobile' => $client->phone_mobile,
                'address' => $client->address,
            ]
        ], false, true);
        if($response['code']==200 || $response['code']==201) {
            return true;
        }else{
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
            $response = parent::getRequest(ClientRequest::BASE_URL.'/'.$client_id, RestRequest::METHOD_DELETE, [ ], false, true);
            return ($response['code']==200 || $response['code']==201);
        }

        return false;
    }
}
