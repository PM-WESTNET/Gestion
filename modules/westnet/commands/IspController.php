<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:46
 */

namespace app\modules\westnet\commands;

use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\westnet\isp\IspFactory;
use app\modules\westnet\isp\IspInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\isp\wispro\ClientRequest;
use app\modules\westnet\isp\wispro\ContractRequest;
use app\modules\westnet\isp\wispro\WisproRequest;
use app\modules\westnet\models\Server;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;

class IspController extends Controller
{
    /**
     *  Muestra todos los usuarios de Mesa.
     */
    public function actionListServers()
    {
        $this->stdout("ISP - Servers\n", Console::BOLD, Console::FG_CYAN);

        foreach( Server::find()->all() as $server ) {
            $this->stdout("Server: " . $server->name . " - ID: " . $server->server_id. " - " . $server->class . "\n");
        }
    }

    /**
     *
     */
    public function actionListClients($server_id)
    {
        // Busco el servidor del que voy a sacar los datos.
        /** @var IspInterface $api */
        $api = $this->getApi($server_id);
        $clients = $api->getClientApi()->listAll();

        foreach($clients as $client) {
            $this->stdout(print_r($client,1));
        }
    }

    public function actionFindClient($server_id, $search)
    {
        // Busco el servidor del que voy a sacar los datos.
        $api = $this->getApi($server_id)->getClientApi();
        $clients = $api->find($search, ClientRequest::Q_NAME);

        if($clients) {
            foreach($clients as $client) {
                $this->stdout(print_r($client,1));
            }
        }
    }

    public function actionFindContract($server_id, $search)
    {
        // Busco el servidor del que voy a sacar los datos.
        $api = $this->getApi($server_id)->getContractApi();

        $contracts = $api->find($search, ContractRequest::Q_CLIENT_ID);

        if($contracts) {
            foreach($contracts as $contract) {
                $this->stdout(print_r($contract,1));
            }
        }
    }

    public function actionFindContractByIp($server_id, $search)
    {
        // Busco el servidor del que voy a sacar los datos.
        $api = $this->getApi($server_id)->getContractApi();

        $contracts = $api->find($search, ContractRequest::Q_IP);

        if($contracts) {
            foreach($contracts as $contract) {
                $this->stdout(print_r($contract,1));
            }
        }
    }

    public function actionUpdateContract($server_id)
    {
        // Creo los Request
        $api = $this->getApi($server_id);
        $clientApi = $api->getClientApi();
        $contractApi = $api->getContractApi();

        $clients = $clientApi->listAll();

        $cClient = 0;
        $cSinContrato = 0;
        $cContrato = 0;
        foreach($clients as $client) {
            $cClient++;
            $contractsWispro = $contractApi->find($client->id, ContractRequest::Q_CLIENT_ID);
            $contracts = Contract::find()
                ->leftJoin('customer cus', 'contract.customer_id = cus.customer_id')
                ->leftJoin('connection con', 'contract.contract_id = con.contract_id')
                ->where(['cus.code'=> $client->external_client_number, 'con.server_id'=>$server_id])->all()
            ;

            try {
                $updated = [];
                foreach($contractsWispro as $contractWispro) {

                    foreach($contracts as $contract) {
                        if(empty($contract->external_id) ){// && array_search($contract->contract_id, $updated)=== false ){
                            $cContrato++;
                            $contract->external_id = $contractWispro->id;
                            //$contract->updateAttributes(['external_id'=>$contractWispro->id]);
                            $updated[] = $contract->contract_id;
                            $this->stdout($client->id . " - Wispro: " . $contractWispro->id . " - ID: " . $contract->contract_id."\n");
                            // Solo pongo la primera que encuentro
                            break;
                        }
                    }
                }

            } catch(\Exception $ex) {
                //var_dump($client->id);
                $cSinContrato++;
            }

        }
        $this->stdout("Clientes: " . $cClient . " - con: " . $cContrato . " - sin: " . $cSinContrato );
    }

    public function actionChangeStatus($server_id, $contract_id)
    {
        $api = $this->getApi($server_id)->getContractApi();
        $contract = new \app\modules\westnet\isp\models\Contract([]);
        $contract->id = $contract_id;
        $contract->state = 'enabled' ;

        error_log( $api->update($contract) );
    }

    public function actionFindPlans()
    {
        $planes = [];
        foreach ( Plan::find()->all() as $plan ){
            $planes[] = strtolower($plan->system);
        }

        $servers = Server::find()->all();
        foreach($servers as $server) {
            $plans = $this->getApi($server)->getPlanApi()->listAll();

            foreach($plans as $plan) {
                echo   $server->name . " - " . $plan['plan']['id']."-" . preg_replace("[ |/]", "-", strtolower($plan['plan']['name'])) . "\n";
            }
        }
    }

    public function actionFindPlan($server_id)
    {

        $api = $this->getApi($server_id)->getPlanApi();

        foreach($api->listAll() as $plan) {
            echo  $plan['plan']['id']."-" . preg_replace("[ |/]", "-", strtolower($plan['plan']['name'])) . "\n";
        }

        $plan = $api->find('fibra-0-1024-0-1024');
        echo $plan['id'];

    }

    public function actionContractControl($server_id)
    {
        /** @var IspInterface $api */
        $api = $this->getApi($server_id);

        // Creo los Request
        $api = $api->getClientApi();
        $apiContract = $api->getContractApi();
        $clients = $api->listAll();

        $cClient = 0;
        $cSinContrato = 0;
        $cContrato = 0;
        foreach($clients as $client) {
            $cClient++;

            $contracts = Contract::find()
                ->leftJoin('customer cus', 'contract.customer_id = cus.customer_id')
                ->leftJoin('connection con', 'contract.contract_id = con.contract_id')
                ->where(['cus.code' => $client->external_client_number, 'con.server_id' => $server_id])->all();


            foreach ($contracts as $contract) {
                if($contract->external_id != null) {
                    $contractsWispro = [];
                    $contractsWispro = $apiContract->find($contract->external_id, ContractRequest::Q_EXTERNAL_ID);
                    if(!empty($contractsWispro)) {
                        foreach ($contractsWispro as $contractWispro) {
                            if ($contractWispro->id != $contract->external_id) {
                                $this->stdout("Cliente: " .$client->name . " - ". $contract->customer_id . " - contract: " . $contract->contract_id."\n");
                            }
                        }
                    }

                }
            }
        }
    }

    public function actionApplyChanges($server_id)
    {
        $server = Server::findOne(['server_id' => $server_id]);
        if ($server) {
            $api = new WisproRequest($server->url, $server->token);

            error_log( $api->apply() ? "ok": "fail" );
        }
    }

    /**
     * Actualiza el external_client_number de Wispro con el Customer code.
     * Solo lo hace para los que tengan customer_id disitinto de code
     */
    public function actionUpdateCustomerCode()
    {
        $query = (new Query())
            ->select(['c.customer_id', 'c.name', 'c.lastname', 'c.code', 'con.external_id', 'cnn.server_id', 's.url', 's.token'])
            ->from('customer c')
            ->leftJoin('contract con', 'c.customer_id = con.customer_id')
            ->leftJoin('connection cnn','cnn.contract_id = con.contract_id')
            ->leftJoin('server s','cnn.server_id = s.server_id')
            ->where('c.customer_id <> c.code and s.server_id is not null')
        ;
        $cons = $query->all();

        foreach ($cons as $con) {
            $api = $this->getApi($con['server_id']);
            $apiCli = $api->getClientApi();
            $customer = Customer::findOne(['customer_id'=> $con['customer_id']]);
            $client = new Client($customer);
            $clientRest = $apiCli->find($con['customer_id'], ClientRequest::Q_EXT_ID)[0];
            if($clientRest) {
                $client->id = $clientRest->id;
                $client->external_client_number = $con['code'];
                $apiCli->update($client);
                $this->stdout("Cliente: " . $client->name . " - " . $clientRest->name ."\n");
            }
        }
    }

    public function actionCreateClient($server_id)
    {
        /** @var IspInterface $api */
        $api = $this->getApi($server_id);

        $client = new Client([
            'name'        => 'Prueba',
            'email'       => 'prueba@prueba.com',
            'phone'       => '261123123',
            'created_at'  => '2017-01-01',
            'city'        => 38,
            'address'     => 'Domicilio',
            'external_client_number'      => '999',
        ]);


        // Creo los Request
        $clientApi = $api->getClientApi();
        $clientApi->create($client);
        error_log("-------------------------------------------");
        error_log(print_r($client,1));
        error_log("-------------------------------------------");
    }

    public function actionUpdateClient($server_id)
    {
        /** @var IspInterface $api */
        $api = $this->getApi($server_id);

        $client = new Client([
            'name'        => 'Prueba 132',
            'email'       => 'prueba@prueba.com',
            'phone'       => '261123123',
            'created_at'  => '2017-01-01',
            'city'        => 38,
            'address'     => 'Nuevo Domicilio',
            'external_client_number'      => '99921',
        ]);


        // Creo los Request
        $clientApi = $api->getClientApi();
        echo $clientApi->update($client) ;
        error_log("-------------------------------------------");
        //error_log(print_r($client,1));
        error_log("-------------------------------------------");
    }

    public function actionDeleteClient($server_id)
    {
        /** @var IspInterface $api */
        $api = $this->getApi($server_id);

        // Creo los Request
        $clientApi = $api->getClientApi();
        echo ($clientApi->delete(999) ? "si": "no" );
        error_log("-------------------------------------------");
        error_log("-------------------------------------------");
    }

    /**
     * @param $server
     * @return IspInterface|null
     */
    private function getApi($server)
    {
        if($server instanceof Server) {
        } else {
            $server = Server::findOne(['server_id'=>$server]);
        }
        if($server) {
            return IspFactory::getInstance()->getIsp($server);
        }
        throw new \Exception('The Server dont exist.');
    }
}
