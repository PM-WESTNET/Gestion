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
        // return false if no server is associated
        if (!isset($connection->server,$connection->server->load_balancer_type)) return false;
        
        // if it isnt a mikrotik type server connection or is disabled
        if (!($connection->server->load_balancer_type == 'Mikrotik') || ($connection->server->status != 'enabled')) return false;


        // if the mikrotik server has a known IP for GestionApp then it is most probably a wireless connection to be updated (without soldef help)
        if (!empty($connection->server->ip_of_load_balancer)){
            $mikrotikIP = long2ip($connection->server->ip_of_load_balancer);

            // create queue on mikrotik server
            $queueCreated = self::createMikrotikQueue($connection, $mikrotikIP);
            
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
    
                $queueDeleted = self::deleteMikrotikQueue($mikrotikIPDelete,$old_ip4_1);
                if (is_string($queueDeleted) && isset(Yii::$app->session)) Yii::$app->session->addFlash('info', $queueDeleted.' on server: '.$connection->server->name);
                if (is_bool($queueDeleted) && isset(Yii::$app->session)) Yii::$app->session->addFlash('error', 'Failed to delete Queue on Mikrotik server'.$connection->server->name);
            }
        } 
        // if IS mikrotik but doesnt have an IP associated, Soldef controls it (FTTH logic)
        else 
        {
            // check if the current connection has an FTTH plan associated, then run Soldef api
            if(!empty(self::getPlanTechnologyCategory($connection))){
                $queueCreated = self::updateMikrotikQueueFTTH($connection, $old_ip4_1);
            }
        }

        // if FALSE
        if (is_bool($queueCreated) && !$queueCreated) {
            if(isset(Yii::$app->session)) Yii::$app->session->addFlash('error', 'Failed to create Queue on Mikrotik server'.$connection->server->name);
            return false;
        }
        else if (is_string($queueCreated) && isset(Yii::$app->session)) Yii::$app->session->addFlash('info', $queueCreated.' on server: '.$connection->server->name);
            
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
    private function deleteMikrotikQueue($mikrotikIP,$old_ip4_1)
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

    /**
     * creates a mikrotik queue with the help of SOLDEF and RADIUS.
     * used for FTTH connections that pass through mikrotik load balancers
     * returns false if theres a failure
     */
    private function updateMikrotikQueueFTTH($connection,$old_ip4_1 = null, $http_method = 'POST'){
        $base_url = $connection->server->url; // gives something like https://172.27.2.31/ or https://soldef.westnet.com.ar/
        $base_url = 'https://soldef.westnet.com.ar/';
        $endpoint = 'api/v1/fibra/Update_Radius_Plan_API';
        $api_url = $base_url.$endpoint;
        $current_ip4_1 = long2ip($connection->ip4_1); // convert to ip. if null or 0 => 0.0.0.0
        $old_ip4_1 = is_null($old_ip4_1) ? $current_ip4_1 : $old_ip4_1; // if there is no old_ip, use current_ip value

        // var_dump($base_url);
        // var_dump($api_url);
        // var_dump($current_ip4_1);

        $queue_data = array(
            "ip_anterior" => $old_ip4_1, // old ip
            "ip_nueva" => $current_ip4_1 //connection->ip
        );
        $data = json_encode($queue_data); //json conv
        // var_dump($data);

        // curl setup
        $conexion = curl_init();
        curl_setopt($conexion, CURLOPT_URL, $api_url);
        curl_setopt($conexion, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $conexion,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, $http_method);
        $respuesta = '';
        $respuesta = curl_exec($conexion);
        $HTTP_CODE_RESPONSE = curl_getinfo($conexion, CURLINFO_HTTP_CODE); // get http response code : 200,401,500...
        // var_dump(curl_error($conexion));
        // var_dump($respuesta);
        curl_close($conexion); // connection close
        // format response
        if (is_array($respuesta)) $respuesta = implode('. ', $respuesta); // if is array, transform into string for response
        $responseString = ($respuesta . ' HTTP_CODE: ' . $HTTP_CODE_RESPONSE);

        return $responseString; //string or false
    }

    /**
     * gets the current connection's contract's plan technology category. 
     * could be wifi or fiber optics but is only used for fiber mikrotik connections.
     * 
     */
    private function getPlanTechnologyCategory($connection)
    {
        // select , from , join
        $query = (new Query())
            ->select(['*'])
            ->from('connection conn')
            ->leftJoin('contract cont', 'conn.contract_id = cont.contract_id')
            ->leftJoin('contract_detail cd', 'cd.contract_id = cont.contract_id')
            ->leftJoin('product prod', 'cd.product_id = prod.product_id')
            ->leftJoin('product_has_category phcat', 'phcat.product_id = prod.product_id')
            ->leftJoin('category cat', 'phcat.category_id = cat.category_id');

        //where
        $query->andWhere(['prod.type' => 'plan']);
        $query->andWhere(['cat.category_id' => '16']); // todo: change this hardcoded ID for FTTH plans . FUCK.
        $query->andWhere(['conn.connection_id' => $connection->connection_id]);

        //exec
        $qResults = $query->one();
        return $qResults;
    }

}
