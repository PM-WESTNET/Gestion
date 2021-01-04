<?php

namespace app\modules\westnet\components\ipStrategy;


use app\modules\westnet\components\ipStrategy\IpAssigmentStrategyInterface;
use app\modules\westnet\models\Connection;
use yii\web\BadRequestHttpException;

class AccessPointStrategy implements IpAssigmentStrategyInterface
{

    /**
     * Devuelve una ip válida para ser asignada a la conexión
     */
    public function getValidIp($node, $ap = null)
    {
        if (empty($ap)) {
            throw new BadRequestHttpException('Access Point is required');
        }

        $range = $ap->getActiveIpRange();

        if ($range === false) {
            return false;
        }

        $ip = $range->getAvailableIp();

        return $ip;
    }
}