<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/01/17
 * Time: 14:52
 */

namespace app\modules\westnet\isp\i815;


use app\modules\westnet\isp\ApiProvider;
use app\modules\westnet\isp\IspInterface;
use app\modules\westnet\isp\ServerInterface;

class I815Isp implements IspInterface {

    /**
     * @var ServerInterface
     */
    private $server;

    public function auth(ServerInterface $server)
    {
        $this->server = $server;
        $rta = (new CurlXml($server->getUrl()))->request('integracion/login/', ['usuario' => $server->getUser(), 'password'=>$server->getPassword()]);
        $this->server->setToken((string)$rta->token);
    }

    public function getClientApi()
    {
        return new Client815Request($this->server->getUrl(), $this->server->getToken());
    }

    public function getPlanApi()
    {
        return new Plans815Request($this->server->getUrl(), $this->server->getToken());
    }

    public function getContractApi()
    {
        return new Contract815Request($this->server->getUrl(), $this->server->getToken());
    }

    /**
     * Retorna la api del proveedor para aplicar cambios.
     * @return ApiProvider
     */
    public function getProviderApi()
    {
        return null;
    }
}