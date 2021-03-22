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
 * Interface ApiInterface
 * Interface a implementar para la comunicacion con el servidor isp.
 *
 * @package app\modules\westnet\isp
 */
interface ApiInterface {

    /**
     * Crea un objeto en el servidor.
     *
     * @param $object
     * @return mixed
     */
    public function create($object);

    /**
     * Actualiza un objeto en el servidor.
     *
     * @param $object
     * @return mixed
     */
    public function update($object);

    /**
     * Borra un objeto en el servidor
     *
     * @param $object
     * @return mixed
     */
    public function delete($object);

    /**
     * Busca un objeto.
     *
     * @param $vale
     * @param int $type
     * @return mixed
     */
    public function find($vale, $type=0);

    /**
     * Lista todos los objetos del servidor.
     *
     * @return mixed
     */
    public function listAll();
}