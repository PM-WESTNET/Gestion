<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/01/17
 * Time: 11:11
 */

namespace app\modules\westnet\isp;

/**
 * Class IspFactory
 * Factory de isps
 *
 * @package app\modules\westnet\isp
 */
class IspFactory
{
    private static $instancia;

    public static function getInstance()
    {
        if(self::$instancia===null) {
            self::$instancia = new IspFactory();
        }

        return self::$instancia;
    }

    /**
     * @param ServerInterface $server
     * @return IspInterface
     */
    public function getIsp(ServerInterface $server)
    {
        $clase = $server->getClass();
        $obj = new $clase();
        $obj->auth($server);
        return $obj;
    }
}