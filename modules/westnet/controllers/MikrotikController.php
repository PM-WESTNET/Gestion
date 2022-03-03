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
use yii\db\Expression;
use yii\db\Query;
/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class MikrotikController extends Controller
{
    /**
     * Return token access api
     */
    public static function updateQueues($connection){
        // die('updateQueues');
        $url = Config::getConfig('mikrotik_url_create_queues');

        $mikrotikIP = long2ip($connection->server->ip_of_load_balancer);
        $contractDetailConnectionData = self::getContractDetailPlanesData($connection);
        $mikrotikConnectionStatus = self::getMikrotikConnectionStatus($connection);

        $data = array(
            "ip" => $mikrotikIP, // mikrotik ip
            "clientes" => array(array(
                "cliente_ip"=>long2ip($connection->ip4_1), //connection->ip
                "download"=>$contractDetailConnectionData['download'], //planes->download
                "upload"=>$contractDetailConnectionData['upload'], // planes->upload
                "estado"=>$mikrotikConnectionStatus // connection->status
            ))
        );
        $dataJSON = json_encode($data);
        var_dump($dataJSON);

        $conexion = curl_init();
        curl_setopt($conexion, CURLOPT_URL,$url->item->description);
        curl_setopt($conexion, CURLOPT_POSTFIELDS, $dataJSON);
        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'POST'); 

        $respuesta=curl_exec($conexion);

        curl_close($conexion);
        
        Yii::$app->session->addFlash('info', $respuesta);
        return $respuesta;
    }

    private function getContractDetailPlanesData($connection){
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

    private function getMikrotikConnectionStatus($connection){
        $mtikPossibleStatuses = ['inactivo','activo'];
        $connectionEnumPossible = ['enabled','disabled','forced','low'];
        //['enabled','disabled','forced','low'];
        $mikrotikConnectionStatus = false;
        switch ($connection->status){
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
        return (!$mikrotikConnectionStatus)?$mtikPossibleStatuses[0]:$mtikPossibleStatuses[1];
    }
}