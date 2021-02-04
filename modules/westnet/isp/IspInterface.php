<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/01/17
 * Time: 10:14
 */

namespace app\modules\westnet\isp;


/**
 * Interface IspInterface
 * Interface a implementar por los wrappers de los ISP
 *
 * @package app\modules\westnet\isp
 */
interface IspInterface
{
    /**
     * Metodo para la autenticacion en el servidor.
     *
     * @param ServerInterface $server
     * @return bool
     */
    public function auth(ServerInterface $server);

    /**
     * Retorna la implementacion de la api de cliente.
     *
     * @return ApiInterface
     */
    public function getClientApi();

    /**
     * Retorna la implementacion de la api de Planes.
     *
     * @return ApiInterface
     */
    public function getPlanApi();

    /**
     * Retorna la implementacion de la api de Contratos.
     *
     * @return ApiInterface
     */
    public function getContractApi();

    /**
     * Retorna la api del proveedor para aplicar cambios.
     * @return ApiProvider
     */
    public function getProviderApi();

}