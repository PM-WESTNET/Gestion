<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 15/02/16
 * Time: 15:07
 */

namespace app\modules\westnet\components;


use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\isp\IspFactory;
use app\modules\westnet\isp\IspInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\isp\Profiler;
use app\modules\westnet\isp\wispro\ContractRequest;
use app\modules\westnet\isp\wispro\WisproIsp;
use app\modules\westnet\isp\wispro\WisproRequest;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use app\modules\config\models\Config;
use app\modules\westnet\models\Server;
use Yii;

class SecureConnectionUpdate
{
    public static function update(Connection $connection, Contract $contract, $updateConnection = false)
    {

        /** @var Connection $connection */
        if ($connection) {
            /** @var IspInterface $api */
            Profiler::profile('isp');
            $api = IspFactory::getInstance()->getIsp($connection->server);
            Profiler::profile('isp');
            // Como existe la conexion, creo los request
            $clientRequest      = $api->getClientApi();
            $contractRequest    = $api->getContractApi();
            $plansRequest       = $api->getPlanApi();

            // Envio los datos del cliente, si no existe se crea y con el id devuelto el contrato
            $client = new Client( $contract->customer );
            try {
                if (!Config::getValue('app_testing')) {
                    if ($client->external_client_number && !$connection->clean) {
                        $clientRequest->update($client);
                    } else {
                        Profiler::profile('client-create');
                        $client = $clientRequest->create($client);
                        Profiler::profile('client-create');
                    }
                }
            } catch(\Exception $ex) {
                if(Yii::$app->session) {
                    Yii::$app->session->addFlash('error', Yii::t('westnet', 'The connection cant\'t be updated in Server. {error}', ['error' => "Problema con el Cliente. " . $ex->getMessage()]));
                }
            }
            $deleted = false;
            // Si se cambio de nodo tengo que borrar en el nodo anterior y crear en el nuevo
            if( $connection->isNodeChanged() ) {
                Profiler::profile('node-find');
                $node = Node::findOne($connection->old_node_id);
                Profiler::profile('node-find');
                $apiOld = IspFactory::getInstance()->getIsp($node->server);
                $oldServer = $apiOld->getContractApi();

                Profiler::profile('old-server-delete');
                $isDeleted = $oldServer->delete($contract->external_id);
                if( $isDeleted === false){
                    $connection->revertChangeNode();
                    if(isset(Yii::$app->session)) {
                        Yii::$app->session->addFlash('error', Yii::t('westnet', 'The connection cant\'t be deleted in Server. {error}', ['error' => 'Nodo']));
                    }
                } else {
                    $deleted = true;
                    $contract->external_id = null;
                }
                Profiler::profile('old-server-delete');
            }
            // Por ahora solo deberia de pasar cuando cambio el servidor
            if ($connection->clean) {
                if($connection->old_server_id) {
                    $old_server = Server::findOne(['server_id'=>$connection->old_server_id]) ;
                    $apiOld = IspFactory::getInstance()->getIsp($old_server);

                    Profiler::profile('old-server-delete-2');
                    $oldServer = $apiOld->getContractApi();
                    $isDeleted = $oldServer->delete($contract->external_id);
                    if( $isDeleted === false && !$deleted ){
                        $connection->revertChangeNode();
                        if(isset(Yii::$app->session)) {
                            Yii::$app->session->addFlash('error', Yii::t('westnet', 'The connection cant\'t be deleted in Server. {error}', ['error' => 'Servidor']));
                        }
                    } else {
                        $contract->external_id = null;
                    }
                    Profiler::profile('old-server-delete-2');
                }
            }

            $client_id = (is_object($client) ? $client->id : $client );
            if ($client_id) {
                // Traigo el plan
                Profiler::profile('plan-find');

                $plan = $plansRequest->find($contract->getPlan()->system);
                Profiler::profile('plan-find');
                // Controlo si el plan existe en el servidor
                if(!$plan) {
                    if (isset(Yii::$app->session)) {
                        Yii::$app->session->addFlash('error', Yii::t('westnet', 'The plan not exist in the Server. {error}', ['']));
                    }
                    return;
                }

                $contractRest = new \app\modules\westnet\isp\models\Contract($contract, $connection, $plan['id']);
                $contractRest->client_id = $client_id;
                try {

                    Profiler::profile('contract-find');
                    $contractOrig = $contractRequest->find($contractRest->ip, ContractRequest::Q_IP);
                    if(!$contractOrig) {
                        $contractOrig = $contractRequest->find($contractRest->external_id, ContractRequest::Q_EXTERNAL_ID);
                    }
                    Profiler::profile('contract-find');

                    if(is_array($contractOrig) && count($contractOrig)>0) {
                        $contractOrig = $contractOrig[0];
                    }

                    $rta = true;
                    if($contractOrig) {
                        Profiler::profile('contract-udpate');
                        $rta = $contractRequest->update($contractRest);
                        Profiler::profile('contract-udpate');
                    } else {
                        $contract->external_id = null;
                        Profiler::profile('contract-create');
                        $contractRest = $contractRequest->create($contractRest);
                        Profiler::profile('contract-create');

                        if(is_string($contractRest)){
                            throw new \Exception($contractRest, 500);
                        }

                        $contract->external_id = (is_object($contractRest) ? $contractRest->id : $contractRest ); ;
                        Profiler::profile('contract-gestion-update');
                        $contract->updateAttributes(['external_id']);
                        Profiler::profile('contract-gestion-update');
                    }
                    if((isset(Yii::$app->params['apply_wispro']) && Yii::$app->params['apply_wispro']) || !isset(Yii::$app->params['apply_wispro']) &&
                        $api instanceof WisproIsp
                    ) {
                        Profiler::profile('wispro-apply');
                        $providerRequest    = $api->getProviderApi();
                        $providerRequest->apply();
                        Profiler::profile('wispro-apply');
                    }

                    // Si no actualizo vuelvo atraz y pongo mensaje.
                    if(!$rta)  {
                        $connection->status_account = $connection->old_status_account;
                        Profiler::profile('connection-gestion-update');
                        $connection->updateAttributes(['status']);
                        Profiler::profile('connection-gestion-update');
                        if(isset(Yii::$app->session)) {
                            Yii::$app->session->addFlash('error', Yii::t('westnet', 'The connection cant\'t be updated in Server. {error}', ['error' => '']));
                        }
                    }
                } catch( \Exception $ex) {
                    if(isset(Yii::$app->session)) {
                        Yii::$app->session->addFlash('error', Yii::t('westnet', 'The connection cant\'t be updated in Server. {error}', ['error'=> "Problema con la Conexion: ".$ex->getMessage()]));
                    }
                    error_log($ex->getFile() . " - " . $ex->getLine() . " - " . $ex->getMessage());
                    error_log($ex->getTraceAsString());
                }
            }
        }
    }
}