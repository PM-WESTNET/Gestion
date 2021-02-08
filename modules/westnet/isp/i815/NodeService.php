<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 7/06/17
 * Time: 6:53
 */

namespace app\modules\westnet\isp\i815;


use app\modules\westnet\isp\Profiler;

class NodeService
{
    private static $_nodes = null;
    private static $_services = null;

    public static function getNode($base_url, $token, $node_name)
    {
        if (self::$_nodes === null) {
            Profiler::profile('find-nodes');
            try {
                $nodos = (new CurlXml( $base_url, $token))->request('integracion/hardware/nodored/listar/');
                self::$_nodes =  [];
                foreach ($nodos->object as $nodo) {
                    self::$_nodes[(string)$nodo['pk']] = (string)$nodo->field[0];
                }
            } catch (\Exception $ex){
                error_log('Error getNode: ' . $ex->getMessage());
            }
            Profiler::profile('find-nodes');
        }
        error_log(print_r(self::$_nodes,1));
        if(($i=array_search($node_name, self::$_nodes))!==false) {
            return $i;
        }

        return null;
    }

    public static function getService($base_url, $token, $node_id)
    {
        if (self::$_services === null) {
            self::$_services = [];
        }

        if(array_key_exists($node_id, self::$_services)===false) {
            Profiler::profile('find-nodes-service');
            error_log("getService: " . $node_id);
            $servicio = null;
            try{
                $servicio = (new CurlXml( $base_url, $token))->request('integracion/hardware/nodored/listar_servicios/', [
                    'pk' => $node_id,
                ]);
                self::$_services[$node_id] = (string)$servicio->ipest->servicio->pk[0];
            } catch (\Exception $ex ) {
                error_log($ex->getMessage() . " - nodo: " . $node_id. " - " . print_r($servicio,1));
            }
            Profiler::profile('find-nodes-service');
        }
        if(array_key_exists($node_id, self::$_services)!==false) {
            return self::$_services[$node_id];
        } else {
            return null;
        }
    }
}