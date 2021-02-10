<?php

namespace app\modules\westnet\components\ipStrategy;


interface IpAssigmentStrategyInterface 
{

    /**
     * Devuelve una ip válida para ser asignada a la conexión
     */
    public function getValidIp($node, $ap = null);

}