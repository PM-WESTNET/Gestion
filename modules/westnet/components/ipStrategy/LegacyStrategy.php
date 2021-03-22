<?php


namespace app\modules\westnet\components\ipStrategy;

use yii\db\Query;

/**
 * Define la antigua estrategia de asignación de IP
 */
class LegacyStrategy implements IpAssigmentStrategyInterface
{
    /**
     * Devuelve una ip válida para ser asignada a la conexión
     */
    public function getValidIp($node, $ap = null) 
    {
        $ipRange = $node->getIpRange()->one();

        if (empty($ipRange)) {
            return false;
        }

        $start = $ipRange->ip_start;
        $end = $ipRange->ip_end;

        $validIp = false;
        do{
            // Genero un nro aleatorio para el rango
            $ip = rand($start, $end);
            // Si la ip es par, la hago impar
            $ip = (($ip%2)==0 ? $ip : $ip+1 );
            $nodo = ip2long('10.'.$node->subnet.'.0.0');
            $oct = $ip - $nodo;

            preg_match("/\.0$/", long2ip($oct) , $output);
            $validIp = !count($output);
        } while(!$validIp);

        $cant = (new Query())->from('connection')
            ->where("connection.ip4_1 - " . $nodo . " = " . $oct)
            ->count("*");


        if($cant==0) {
            return $ip;
        } else {
            return $this->getValidIp($node);
        }
    }
}