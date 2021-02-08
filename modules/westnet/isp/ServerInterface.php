<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/01/17
 * Time: 13:00
 */

namespace app\modules\westnet\isp;


/**
 * Interface ServerInterface
 * Interface a implementar por los servidores.
 *
 * @package app\modules\westnet\isp
 */
interface ServerInterface {
    /**
     * Retorna la URL de conexion al servidor.
     * @return mixed
     */
    public function getUrl();

    /**
     * Retorna el usuario con el que se conecta al servidor
     *
     * @return mixed
     */
    public function getUser();

    /**
     * Retorna la contraseña con el que se conecta al servidor.
     * @return mixed
     */
    public function getPassword();

    /**
     * Retorna el token con el que se conecta al servidor
     * @return mixed
     */
    public function getToken();

    /**
     * Setea el token con el que se conecta al servidor
     * @return mixed
     */
    public function setToken($token);

    /**
     * Retorna la clase que implementa el isp
     * @return mixed
     */
    public function getClass();
}