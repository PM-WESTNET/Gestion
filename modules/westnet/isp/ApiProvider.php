<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/01/17
 * Time: 13:03
 */

namespace app\modules\westnet\isp;

use app\modules\westnet\isp\models\Client;

/**
 * Interface ApiProvider
 * Interface a implementar para funciones particulares del proveedor
 *
 * @package app\modules\westnet\isp
 */
interface ApiProvider {

    /**
     * Aplica cambios al servidor
     *
     * @return mixed
     */
    public function apply();
}