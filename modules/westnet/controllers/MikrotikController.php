<?php

namespace app\modules\westnet\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\config\models\Config;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\db\Expression;
use yii\db\Query;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class MikrotikController extends Controller
{
    /**
     * Returns a string response or false in case of any errors.
     * also adds a flash to display the data in case of string response success.
     */
    public static function updateQueues($connection, $old_ip4_1 = null)
    {
        // return false if no server is associated OR of it isnt a mikrotik type server connection
        if (!isset($connection->server,$connection->server->load_balancer_type) or !($connection->server->load_balancer_type == 'Mikrotik')) return false;
        $mikrotikIP = long2ip($connection->server->ip_of_load_balancer);
        $responseInfo = false; // defaults as false

        // create queue on mikrotik server
        $queueCreated = self::createMikrotikQueue($connection, $mikrotikIP);
        var_dump($queueCreated);
        // delete queue in mikrotik server
        if (!is_null($old_ip4_1)) {
            $queueDeleted = self::deleteMikrotikQueue($connection,$mikrotikIP,$old_ip4_1);
            var_dump($queueDeleted);
        }
        // var_dump($queueCreated,$queueDeleted);
        return $responseInfo; // returns false if error
    }

    /**
     * creates a queue on a Mikrotik server based on its IP and the current connections data.
     */
    private function createMikrotikQueue($connection, $mikrotikIP)
    {
        $contractDetailConnectionData = self::getContractDetailPlanesData($connection);
        $mikrotikConnectionStatus = self::getMikrotikConnectionStatus($connection);
        // A queue to create in a mikrotik server
        $queueAdd = array(
            "cliente_ip" => long2ip($connection->ip4_1), //connection->ip
            "download" => $contractDetailConnectionData['download'], //planes->download
            "upload" => $contractDetailConnectionData['upload'], // planes->upload
            "estado" => $mikrotikConnectionStatus // connection->status
        );

        $dataAdd = array(
            "ip" => $mikrotikIP, // mikrotik ip
            "clientes" => array($queueAdd) //* you can add multiple queues to update here
        );
        // create/update Queue from queuesAPI
        $responseInfo = self::setUpdatedQueues(json_encode($dataAdd), 'POST');
        if (is_string($responseInfo)) Yii::$app->session->addFlash('info', $responseInfo);

        return $responseInfo;
    }

    /**
     * deletes a queue on a Mikrotik server based on its IP and the current connections data.
     */
    private function deleteMikrotikQueue($connection,$mikrotikIP,$old_ip4_1)
    {
        $dataDel = array(
            "ip" => $mikrotikIP, // mikrotik ip
            "clientes" => array(
                array(
                    "cliente_ip" => long2ip($old_ip4_1) // old ip queue to delete
                )
            ) //* you can add multiple queues to update here
        );
        // create/update Queue from queuesAPI
        $responseInfo = self::setUpdatedQueues(json_encode($dataDel), 'DELETE');
        if (is_string($responseInfo)) Yii::$app->session->addFlash('info', $responseInfo);

        return $responseInfo;
    }

    /**
     * joins the connection model (w contract_id) to the planes view to get info about the current plan's download, upload, name, etc.
     */
    private function getContractDetailPlanesData($connection)
    {
        // select , from , join
        $query = (new Query())
            ->select(['*'])
            ->from('contract_detail cd')
            ->leftJoin('planes p', 'cd.product_id = p.product_id');
        //where
        $query->andWhere(['=', 'cd.contract_id', $connection->contract_id]);
        //exec
        $qResults = $query->one();
        return $qResults;
    }

    /**
     * returns 'inactivo' or 'activo' based on the current connection status.
     */
    private function getMikrotikConnectionStatus($connection)
    {
        $mtikPossibleStatuses = ['inactivo', 'activo'];
        $connectionEnumPossible = ['enabled', 'disabled', 'forced', 'low'];
        //['enabled','disabled','forced','low'];
        $mikrotikConnectionStatus = false;
        switch ($connection->status) {
            case $connectionEnumPossible[0]:
                $mikrotikConnectionStatus = true;
                break;
            case $connectionEnumPossible[1]:
                $mikrotikConnectionStatus = false;
                break;
            case $connectionEnumPossible[2]:
                $mikrotikConnectionStatus = true;
                break;
            case $connectionEnumPossible[3]:
                $mikrotikConnectionStatus = false;
                break;
        }
        return (!$mikrotikConnectionStatus) ? $mtikPossibleStatuses[0] : $mtikPossibleStatuses[1];
    }

    /**
     * creates a connection with the queues API
     * tries to update (create if none, update if exists) a queue info.
     * returns false in case of error
     */
    private function setUpdatedQueues($data, $httpMethod)
    {
        if(!isset(Config::getConfig('mikrotik_url_create_queues')->item->description)) return 'Configuration item mikrotik_url_create_queues not setted.';
        if(!isset(Config::getConfig('mikrotik_access_token_queues')->item->description)) return 'Configuration item mikrotik_access_token_queues not setted.';
        
        // get info from configuration
        $url = Config::getConfig('mikrotik_url_create_queues')->item->description;
        $accessToken = Config::getConfig('mikrotik_access_token_queues')->item->description;
        if (!(isset($url) && isset($accessToken))) return false; // if the config variables arent set , return false

        // curl setup
        $conexion = curl_init();
        curl_setopt($conexion, CURLOPT_URL, $url);
        curl_setopt($conexion, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $conexion,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            )
        );
        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, $httpMethod);
        $respuesta = '';
        $respuesta = curl_exec($conexion);
        $HTTP_CODE_RESPONSE = curl_getinfo($conexion, CURLINFO_HTTP_CODE); // get http response code : 200,401,500...
        curl_close($conexion); // connection close

        // format response
        if (is_array($respuesta)) $respuesta = implode('. ', $respuesta); // if is array, transform into string for response
        $responseString = ($respuesta . ' HTTP_CODE: ' . $HTTP_CODE_RESPONSE);

        return $responseString; //string or false
    }
}
