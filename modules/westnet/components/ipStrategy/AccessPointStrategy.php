<?php

namespace app\modules\westnet\components\ipStrategy;


use app\modules\westnet\components\ipStrategy\IpAssigmentStrategyInterface;

class AccessPointStrategy implements IpAssigmentStrategyInterface
{

    /**
     * Devuelve una ip válida para ser asignada a la conexión
     */
    public function getValidIp($node, $ap = null)
    {

    }
}