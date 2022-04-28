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
use app\modules\westnet\models\Node;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class MikrotikController extends Controller
{
    /**
     * Returns a string response or false in case of any errors.
     * also adds a flash to display the data in case of string response success.
     */
    public static function updateQueues($connection, $old_ip4_1 = null, $old_node_id = null)
    {
        // return false if no server is associated OR of it isnt a mikrotik type server connection
        if (!isset($connection->server,$connection->server->load_balancer_type,$connection->server->ip_of_load_balancer) or 
        !($connection->server->load_balancer_type == 'Mikrotik')) return false;
        $mikrotikIP = long2ip($connection->server->ip_of_load_balancer);
        $responseInfo = false; // defaults as false

        // create queue on mikrotik server
        $queueCreated = self::createMikrotikQueue($connection, $mikrotikIP);
        if (is_string($queueCreated) && isset(Yii::$app->session)) Yii::$app->session->addFlash('info', $queueCreated.' on server: '.$connection->server->name);
        
        // if FALSE
        if (is_bool($queueCreated) && !$queueCreated) {
            if(isset(Yii::$app->session)) Yii::$app->session->addFlash('error', 'Failed to create Queue on Mikrotik server'.$connection->server->name);
            return false;
        }
        
        // delete queue in mikrotik server
        if (!is_null($old_ip4_1)) {
            $mikrotikIPDelete = $mikrotikIP;

            // delete the queue of the server where it was left off last time (could be a different mikrotik server)
            if (!is_null($old_node_id)) {
                // var_dump('$old_node_id',$old_node_id);
                $oldNode = Node::findOne($old_node_id); // in case the node changed, we search for it and its server's IP number
                $mikrotikIPDelete = long2ip($oldNode->server->ip_of_load_balancer);
                // var_dump('$mikrotikIPDelete',$mikrotikIPDelete);
            }

            $queueDeleted = self::deleteMikrotikQueue($connection,$mikrotikIPDelete,$old_ip4_1);
            if (is_string($queueDeleted) && isset(Yii::$app->session)) Yii::$app->session->addFlash('info', $queueDeleted.' on server: '.$connection->server->name);
            if (is_bool($queueDeleted) && isset(Yii::$app->session)) Yii::$app->session->addFlash('error', 'Failed to delete Queue on Mikrotik server'.$connection->server->name);
        }

        return $queueCreated;
    }

    /**
     * creates a queue on a Mikrotik server based on its IP and the current connections data.
     */
    private function createMikrotikQueue($connection, $mikrotikIP)
    {
        $contractDetailConnectionData = self::getContractDetailPlanesData($connection);
        $mikrotikConnectionStatus = self::getMikrotikConnectionStatus($connection);
        $cliente_ip = long2ip($connection->ip4_1); // convert to ip. if null or 0 => 0.0.0.0

        // check if any value is not valid
        if( "0.0.0.0" == $cliente_ip || //ip sent to mikrotik server CANNOT be 0.0.0.0
            empty($contractDetailConnectionData['download']) ||
            empty($contractDetailConnectionData['upload'])
            ){
                // output error msgs
                if(isset(Yii::$app->session))
                {
                    if("0.0.0.0" == $cliente_ip) Yii::$app->session->addFlash('error', 'IP value cannot be 0.0.0.0');
                    if(empty($contractDetailConnectionData['download'])) Yii::$app->session->addFlash('error', 'Download speed of plan is null');
                    if(empty($contractDetailConnectionData['upload'])) Yii::$app->session->addFlash('error', 'Upload speed of plan is null');
                }
                return false;
            }

        // A queue to create in a mikrotik server
        $queueAdd = array(
            "cliente_ip" => $cliente_ip, //connection->ip
            "download" => $contractDetailConnectionData['download'], //planes->download
            "upload" => $contractDetailConnectionData['upload'], // planes->upload
            "estado" => $mikrotikConnectionStatus // connection->status
        );

        $dataAdd = array(
            "ip" => $mikrotikIP, // mikrotik ip
            "clientes" => array($queueAdd) //* you can add multiple queues to update here
        );
        // create/update Queue from queuesAPI
        $responseInfo = self::setUpdatedQueues(json_encode($dataAdd), 'PUT');
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
            ->leftJoin('product prod', 'cd.product_id = prod.product_id')
            ->leftJoin('planes p', 'prod.product_id = p.product_id');
        //where
        $query->andWhere(['=', 'cd.contract_id', $connection->contract_id]);
        $query->andWhere(['prod.type' => 'plan']);
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
