<?php

namespace app\modules\westnet\isp\wispro;


use app\modules\westnet\isp\ApiProvider;
use app\modules\westnet\isp\IspInterface;
use app\modules\westnet\isp\ServerInterface;

class MikrotikIsp implements IspInterface {

    /**
     * @var ServerInterface
     */
    private $server;

    public function auth(ServerInterface $server)
    {
        $this->server = $server;
    }

    public function getClientApi()
    {
        return new ClientRequest($this->server->getUrl(), $this->server->getToken());
    }

    public function getPlanApi()
    {
        return new PlansRequest($this->server->getUrl(), $this->server->getToken());
    }

    public function getContractApi()
    {
        return new ContractRequest($this->server->getUrl(), $this->server->getToken());
    }

    /**
     * Retorna la api del proveedor para aplicar cambios.
     * @return ApiProvider
     */
    public function getProviderApi()
    {
        return new MikrotikRequest($this->server->getUrl(), $this->server->getToken());
    }
}