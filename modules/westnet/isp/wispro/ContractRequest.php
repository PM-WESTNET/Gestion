<?php

namespace app\modules\westnet\isp\wispro;

use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\isp\models\Contract;
use app\modules\westnet\sequre\components\RestRequest;
use Yii;

/**
 * Description of ClientRequest
 *
 * @author cgarcia
 */
class ContractRequest extends RestRequest implements ApiInterface
{

    const BASE_URL = 'api/contracts';

    const Q_EXTERNAL_ID = 0;
    const Q_CLIENT_ID = 1;
    const Q_ID = 2;
    const Q_IP = 3;

    public function __construct($base_url, $token='')
    {
        parent::__construct($base_url, $token);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
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
            $contractOrig = $this->find($contract->external_id, ContractRequest::Q_EXTERNAL_ID);
            if($contractOrig) {
                return $this->update($contract);
            }
        }

        //Creates a new request for contract creation
        $data = [
            'external_id' => $contract->external_id,
            'plan_id' => $contract->plan_id,
            'client_id' => $contract->client_id,
            'ceil_dfl_percent' => $contract->ceil_dfl_percent,
            'state' => $contract->state,
            'node' => $contract->node,
            'id' => $contract->id
        ];
        if($contract->ip != null) {
            $data['ip'] = $contract->ip;
        }

        $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_POST, [
            'contract' => $data
        ], false, true);

        if($response['code'] == 200 || $response['code'] == 201) {
            $contract->id = $response['response']['contract']['id'];
            return $contract;
        } else {
            return $response['errorno'].': '.$response['error'];
        }
    }

    /**
     * Retorno todos los contratos de un servidor
     * @return array|bool
     */
    public function listAll() {
        $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_GET, [], false, true);
        if((!empty($response) && !empty($response['response']) ) && ($response['code']==200 || $response['code']==201)) {
            $contract = [];
            // Siempre trae un array con otro array llamado client
            foreach($response['response'] as $key=>$value ) {
                $contract[] = new Contract($value['contract']);
            }

            return $contract;
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
    public function find($id = 0, $type = ContractRequest::Q_EXTERNAL_ID )
    {
        if($type == ContractRequest::Q_EXTERNAL_ID) {
            $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_GET, ['search' => ['external_id_eq' => $id]], false, true);
        } elseif($type == ContractRequest::Q_ID){
            $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_GET, ['search'=>['id_equals'=>$id]], false, true);
        } elseif($type == ContractRequest::Q_IP){
            $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_GET, ['search'=>['ip_equals'=>$id]], false, true);
        } else {
            $response = parent::getRequest(ContractRequest::BASE_URL, RestRequest::METHOD_GET, ['search'=>['client_id_eq'=>$id]], false, true);
        }
        if((!empty($response) && !empty($response['response']) ) && ($response['code']==200 || $response['code']==201)) {
            $contract = [];
            // Siempre trae un array con otro array llamado client
            foreach($response['response'] as $key=>$value ) {
                $contract[] = new Contract($value['contract']);
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
        $contractOrig = $this->find($contract->id, ContractRequest::Q_ID);

        if(!$contractOrig) {
            return false;
        }

        $contractOrig = $contractOrig[0];
        $contractOrig->merge($contract);

        $contractOrig->updated_at = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $data = [
            'external_id' => $contract->external_id,
            'plan_id' => $contract->plan_id,
            'client_id' => $contract->client_id,
            'ceil_dfl_percent' => $contract->ceil_dfl_percent,
            'state' => $contract->state,
            'node' => $contract->node,
            'id' => $contract->id
        ];
        if($contract->ip != null) {
            $data['ip'] = $contract->ip;
        }

        // El update no devuelve nada
        $response = parent::getRequest(ContractRequest::BASE_URL. '/'.$contract->id, RestRequest::METHOD_PUT, [
            'contract' => $data
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
    public function delete($contract_id)
    {
        if($contract_id) {
            $response = parent::getRequest(ContractRequest::BASE_URL.'/'.$contract_id, RestRequest::METHOD_DELETE, ['action' => 'destroy', 'controller'=>'api/contracts' ], false, true);

            return ($response['code']==200 || $response['code']==201);
        }

        return false;
    }

}