<?php

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 15:46
 */

namespace app\modules\westnet\commands;

use app\modules\checkout\models\Payment;
use app\modules\config\models\Config;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\westnet\components\SecureConnectionUpdate;
use app\modules\westnet\isp\IspFactory;
use app\modules\westnet\isp\IspInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\isp\wispro\ClientRequest;
use app\modules\westnet\isp\wispro\ContractRequest;
use app\modules\westnet\mesa\components\models\Categoria;
use app\modules\westnet\mesa\components\request\CategoriaRequest;
use app\modules\westnet\mesa\components\request\TicketRequest;
use app\modules\westnet\mesa\components\request\UsuarioRequest;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Server;
use Yii;
use yii\console\Controller;
use yii\db\ActiveQuery;
use yii\db\cubrid\QueryBuilder;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Console;
use app\modules\alertsbot\controllers\TelegramController;

class ConnectionStatusController extends Controller
{

    /**
     * Actualizo los estados de las conexiones en base a la deuda
     * del cliente.
     * Cron que se corre cada 15 minutos aprox. ( actualmente cada *25 mins )
     *
     */
    public function actionUpdate($save = false) // when activating the cron you can pass a parameter (like 1 for TRUE)
    {
        if (Yii::$app->mutex->acquire('update_connections_cron')) {
            $this->updateConnections($save);
            Yii::$app->mutex->release('update_connections_cron');
        }
    }


    /**
     * Actualizo los cambios de plan.
     */
    public function actionUpdatePlan($date = null, $from_date = null, $limit = null)
    {
        if (Yii::$app->mutex->acquire('update_plans_cron')) {
            $this->updatePlans($date, $from_date, $limit);
            Yii::$app->mutex->release('update_plans_cron');
        }
    }

    public function actionCorregirPlans(array $server_id = null, array $plan_id = null, $limit = null)
    {
        if (Yii::$app->mutex->acquire('corregir_planes')) {
            $this->corregirPlanes($limit, $server_id, $plan_id);
            Yii::$app->mutex->release('corregir_planes');
        }
    }

    /**
     * Actualizo todos los planes.
     * 
     * The newer comments are an attempt to document the cronjob previously not knowing anything about it. beware.
     * This function updates all customer's contracts and plans.
     * logs to >> /var/log/connection-update-all.log
     */
    public function actionUpdateAll()
    {
        $this->stdout("Westnet - Proceso de actualizacion de todos los clientes y contratos - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);

        // get all active contracts and join them to other later needed tables. ('active' contracts strongly relates to real active customers)
        $query = (new Query())
            ->select([
                'c.customer_id', 'c.contract_id', 'cus.code',  'cd.contract_detail_id', 'c.external_id',
                'p.system', 's.server_id', 's.url', 's.token', new Expression('inet_ntoa(ip4_1) as ip')
            ])
            ->from('contract c')
            ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
            ->leftJoin('customer cus', 'c.customer_id = cus.customer_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('connection con', 'c.contract_id = con.contract_id')
            ->leftJoin('server s', 'con.server_id = s.server_id')
            ->where(['p.type' => 'plan', 'c.status' => 'active']);

        // execute query. get array of models
        $contracts = $query->all();

        // define variables job process
        $apis = [];
        $clientRequests = [];
        $contractRequests = [];
        $planes = [];
        $updated = 0;

        // this app param variable seems to be used later on SecureConnectionUpdate to apply changes via API to the wispro servers
        Yii::$app->params['apply_wispro'] = false;

        try {
            $wispro = new SecureConnectionUpdate();

            foreach ($contracts as $contract_q) {
                if (array_key_exists($contract_q['server_id'], $apis) === false) {
                    $server = Server::findOne($contract_q['server_id']);
                    /** @var IspInterface $api */
                    $api = IspFactory::getInstance()->getIsp($server);
                    $api->auth($server);
                    $apis[$contract_q['server_id']] = $api;
                }
                $api = $apis[$contract_q['server_id']];

                if (array_key_exists($contract_q['server_id'], $contractRequests) === false) {
                    // Como existe la conexion, creo los request
                    $clientRequests[$contract_q['server_id']]      = $api->getClientApi();
                    $contractRequests[$contract_q['server_id']]    = $api->getContractApi();
                }
                if (array_key_exists($contract_q['server_id'] . "_" . $contract_q['system'],  $planes) === false) {
                    $this->saveTime('planapi-listall');
                    $plansRequest = $api->getPlanApi();
                    $plans = $plansRequest->listAll();
                    $this->saveTime('planapi-listall');
                    if(is_bool($plans)){
                        // this error is most probably due to FTTH plans or something.
                        $this->stdout('"Get Plan API" failed for customer:'.$contract_q['customer_id'].' contract id:'.$contract_q['contract_id']."\n");
                        $this->stdout('Continue to next contract'."\n");
                        continue;
                    }

                    foreach ($plans as $plan) {
                        $planes[$contract_q['server_id'] . "_" . preg_replace("[ |/]", "-", strtolower($plan['plan']['name']))] = $plan['plan']['id'];
                    }
                }

                $contractRequest =  $contractRequests[$contract_q['server_id']];
                /** @var ClientRequest $clientRequest */
                $clientRequest =  $clientRequests[$contract_q['server_id']];

                // Verifico que exista el plan
                if (array_key_exists($contract_q['server_id'] . "_" . $contract_q['system'], $planes) !== false) {
                    $plan_id = $planes[$contract_q['server_id'] . "_" . $contract_q['system']];

                    $contract = Contract::findOne(['contract_id' => $contract_q['contract_id']]);
                    $connection = Connection::findOne(['contract_id' => $contract_q['contract_id']]);

                    $contractRest = new \app\modules\westnet\isp\models\Contract($contract, $connection, $plan_id);
                    $clientRest = new Client($connection->contract->customer);


                    try {
                        // Busco el cliente y lo actualizo
                        $this->saveTime('client-find');
                        $customer_api = $clientRequest->find($clientRest->external_client_number, ClientRequest::Q_EXT_ID)[0];
                        $this->saveTime('client-find');
                        if ($customer_api) {
                            $customer_api->merge($clientRest);
                            $this->saveTime('client-update');
                            $clientRequest->update($customer_api);
                            $this->saveTime('client-update');
                        }

                        // Busco el contrato por IP
                        $this->saveTime('contract-find');
                        $contract_api = $contractRequest->find($contractRest->ip, ContractRequest::Q_IP)[0];
                        $this->saveTime('contract-find');
                        if ($contract_api) {
                            if ($contract_api->id != $contract->external_id) {
                                $contract->external_id = $contract_api->id;
                                $this->stdout("Actualizo external_id: " . $contract_api->id . "\n");
                                $contract->updateAttributes(['external_id']);
                            }

                            $this->saveTime('contract-update');
                            $wispro->update($connection, $contract, true);
                            $this->saveTime('contract-update');

                            $this->stdout($contract_q['customer_id'] . " - " . $contract_q['contract_id'] . "\n", Console::BOLD, Console::FG_GREEN);
                            $updated++;
                        }
                    } catch (\Exception $ex) {
                        $this->stdout("Error al Actualizar: " . $contract_q['server_id'] . " - " . $contract_q['customer_id'] . " - " . $contract_q['contract_id'] . " - " . $ex->getMessage() . "\n", Console::BOLD, Console::FG_RED);
                        error_log($ex->getTraceAsString());
                        // send error to telegram
                        TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: connection-status/update-all ****', $ex);
                    }
                } else {
                    $this->stdout("Plan No Encontrado: " . $contract_q['customer_id'] . " - " . $contract_q['server_id'] . "_" . $contract_q['system'] . "\n", Console::BOLD, Console::FG_RED);
                }
            }
        } catch (\Exception $ex) {
            $this->stdout("Exepcion.\n", Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getMessage() . " - " . $ex->getLine(), Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getTraceAsString(), Console::BOLD, Console::FG_RED);
            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: connection-status/update-all ****', $ex);
        }
        /** @var IspInterface $api */
        foreach ($apis as $api) {
            if (($providerApi = $api->getProviderApi()) !== null) {
                $this->saveTime('apply');
                $providerApi->apply();
                $this->saveTime('apply');
            }
        }


        $this->stdout("Actualizados: " . $updated . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("-----------------------------------------------------" . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
        foreach ($this->times as $key => $value) {
            $this->stdout(" - " . $key . ': ' . $value . "\n");
        }
        $this->stdout("-----------------------------------------------------", Console::BOLD, Console::FG_CYAN);
    }

    public function actionUpdateActualPlan()
    {

        $query = (new Query())
            ->select(['c.customer_id', 'c.contract_id', 'c.external_id', 'p.system', 's.server_id', 's.url', 's.token'])
            ->from('contract c')
            ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('connection con', 'c.contract_id = con.contract_id')
            ->leftJoin('server s', 'con.server_id = s.server_id')
            ->where('c.external_id is not null');

        $iguales = 0;
        $distintos = 0;
        $i = 0;
        // Itero en todos los servidores
        $servers = Server::find()->all();
        foreach ($servers as $server) {
            /** @var IspInterface $api */
            $api = IspFactory::getInstance()->getIsp($server);
            $contractRequest = $api->getContractApi();

            // Traigo los planes del servidor y los pongo en un array para consultarlos despues.
            $plansRequest = $api->getPlanApi();
            $plans = $plansRequest->listAll();
            foreach ($plans as $plan) {
                $planes[$server->server_id][preg_replace("[ |/]", "-", strtolower($plan['plan']['name']))] = $plan['plan']['id'];
            }

            $queryCon = clone $query;
            // Busco todos los contratos del servidor
            $contracts = $queryCon->andWhere(['s.server_id' => $server->server_id])->all();
            $this->stdout("Server: " . $server->name . " - Contratos encontrados: " . count($contracts) . "\n", Console::BOLD, Console::FG_GREEN);

            foreach ($contracts as $contract) {
                if (array_key_exists($contract['system'], $planes[$server->server_id]) !== false) {
                    $plan = $planes[$server->server_id][$contract['system']];

                    $contractOrig = $contractRequest->find($contract['external_id'], ContractRequest::Q_ID);
                    if ($contractOrig) {
                        $contractOrig = $contractOrig[0];
                        if ($plan && $plan != $contractOrig->plan_id) {
                            $contractOrig->plan_id = $plan;
                            $contractRequest->update($contractOrig);
                            $distintos++;
                        } else {
                            $iguales++;
                        }
                    }
                }
            }
        }
        $this->stdout("Totales" . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Iguales: " . $iguales . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Distintos: " . $distintos . "\n", Console::BOLD, Console::FG_RED);
    }

    private function debtLastBill($customer_id)
    {
        $cs = new CustomerSearch();
        $rs = $cs->searchDebtBills($customer_id);
        if (!$rs) {
            return 0;
        }
        return $rs['debt_bills'];
    }

    private function getBills($customer_id)
    {
        /** @var ActiveQuery $query */
        $query = Bill::find();
        $query
            ->select(['bill.bill_id'])
            ->leftJoin('bill_type bt', 'bill.bill_type_id = bt.bill_type_id')
            ->andWhere(['customer_id' => $customer_id])
            ->andWhere(new Expression('bt.multiplier > 0'));
        return $query->count();
    }

    private function getLastBill($customer_id)
    {
        /** @var ActiveQuery $query */
        $query = Bill::find();
        $query
            ->select(['bill.date'])
            ->leftJoin('bill_type bt', 'bill.bill_type_id = bt.bill_type_id')
            ->andWhere(['customer_id' => $customer_id])
            ->andWhere(new Expression('bt.multiplier > 0'))
            ->andWhere(['bill.status' => Bill::STATUS_CLOSED])
            ->orderBy(['bill.date' => SORT_DESC]);
        return $query->scalar();
    }

    private $times = [];
    private $start = 0;

    private function saveTime($quien)
    {
        if ($this->start) {
            if (!array_key_exists($quien, $this->times)) {
                $this->times[$quien] = 0;
            }
            $this->times[$quien] += microtime(true) - $this->start;
            $this->start  = 0;
        } else {
            $this->start = microtime(true);
        }
    }

    /**
     * Actualizo los estados de las conexiones a los clientes que son deudores y deben 1 factura
     *
     */
    public function actionUpdateDebtorsWithOneBill($save = false)
    {
        $debug = false;
        $due_day = Config::getValue('bill_due_day');
        if (!$due_day) {
            $due_day = 15;
        }
        $newContractsDays = Config::getValue('new_contracts_days');
        if (!$newContractsDays) {
            $newContractsDays = 0;
        }

        $invoice_next_month = Config::getValue('contract_days_for_invoice_next_month');

        $due_date = new \DateTime(date('Y-m-') . $due_day);
        $due_forced = null;
        $date = new \DateTime('now');
        $newContracts = new \DateTime('now +' . $newContractsDays . " days");
        $last_bill_date = new \DateTime((new \DateTime('now - 1 month'))->format('Y-m-' . $invoice_next_month));
        $bill_date = new \DateTime('last day of this month');

        $estados = [];
        $estadosAnteriores = [];

        $estadosAnteriores[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_LOW] = 0;

        $estados[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estados[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estados[Connection::STATUS_ACCOUNT_LOW] = 0;

        // Traigo todos los customer para poder iterar.
        //        $queryDebtors = Yii::$app->db->createCommand("SELECT SQL_CALC_FOUND_ROWS * FROM (SELECT `customer`.`customer_id`, concat(customer.lastname, ' ', customer.name) as name, `customer`.`phone`, `customer`.`code`, round(coalesce((SELECT sum(b.total * bt.multiplier) as amount FROM `bill` `b` LEFT JOIN `bill_type` `bt` ON b.bill_type_id = bt.bill_type_id WHERE b.status <> 'draft' and b.customer_id = customer.customer_id), 0) - coalesce((SELECT sum(pi.amount) FROM `payment` `p` LEFT JOIN `payment_item` `pi` ON p.payment_id = pi.payment_id and pi.payment_method_id NOT IN(SELECT `payment_method_id` FROM `payment_method` WHERE type='account') WHERE (p.status <> 'cancelled' and p.status <> 'draft') and p.customer_id = customer.customer_id), 0)) as saldo, `bills`.`debt_bills`, `bills`.`payed_bills`, ( bills.debt_bills + bills.payed_bills) as total_bills, `contract_detail`.`product_id` AS `plan`, `customer`.`company_id` AS `customer_company` FROM `customer` LEFT JOIN (SELECT customer_id, sum(qty) as debt_bills, sum(qty_2) AS payed_bills FROM ( SELECT customer_id, date, i, round(amount,2), @saldo:=round(if(customer_id<>@customer_ant and @customer_ant <> 0, amount, @saldo + amount ),2) as saldo, @customer_ant:=customer_id, if((@saldo - (select cc.percentage_tolerance_debt from customer_class_has_customer cchc INNER JOIN (SELECT customer_id, max(date_updated) maxdate FROM customer_class_has_customer GROUP BY customer_id) cchc2 ON cchc2.customer_id = cchc.customer_id AND cchc.date_updated = cchc2.maxdate LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id where cchc.customer_id =a.customer_id)) > 0 and i=1, 1, 0) as qty, if(@saldo <= 0 AND i = 1, 1, 0) as qty_2 FROM ((SELECT `customer_id`, `b`.`date` AS `date`, if(bt.multiplier<0, 0,1) AS i, sum(b.total * bt.multiplier) AS amount FROM bill b FORCE INDEX(fk_bill_customer1_idx) LEFT JOIN `bill_type` `bt` ON b.bill_type_id = bt.bill_type_id WHERE `b`.`status` <> 'draft' GROUP BY `b`.`customer_id`, `b`.`bill_id`) UNION ALL ( SELECT `p`.`customer_id`, `p`.`date` AS `date`, 0 AS i, -p.amount FROM `payment` `p` ) ) a order by customer_id, i, date ) a GROUP BY customer_id ) `bills` ON bills.customer_id = customer.customer_id LEFT JOIN `contract` ON contract.customer_id = customer.customer_id LEFT JOIN `contract_detail` ON contract.contract_id = contract_detail.contract_id INNER JOIN `customer_class_has_customer` `cchc` ON cchc.customer_id= customer.customer_id INNER JOIN (SELECT `customer_id`, max(date_updated) maxdate FROM `customer_class_has_customer` GROUP BY `customer_id`) `cchc2` ON cchc2.customer_id = customer.customer_id and cchc.date_updated = cchc2.maxdate INNER JOIN `customer_category_has_customer` `ccathc` ON ccathc.customer_id= customer.customer_id INNER JOIN (SELECT `customer_id`, max(date_updated) maxdate FROM `customer_category_has_customer` GROUP BY `customer_id`) `ccathc2` ON ccathc2.customer_id = customer.customer_id and ccathc.date_updated = ccathc2.maxdate LEFT JOIN `customer_class` `cc` ON cchc.customer_class_id = cc.customer_class_id LEFT JOIN `customer_category` `ccat` ON ccathc.customer_category_id = ccat.customer_category_id LEFT JOIN `connection` ON connection.contract_id = contract.contract_id LEFT JOIN `node` `n` ON connection.node_id = n.node_id LEFT JOIN `company` ON company.company_id = customer.parent_company_id GROUP BY `customer`.`customer_id`, `customer`.`name`, `customer`.`phone`) `b` WHERE (`saldo` > 0) AND (`debt_bills` >= '1') AND (`debt_bills` <= '1')");

        $customerSearch = new CustomerSearch();
        $customerSearch->debt_bills_from = 1;
        $customerSearch->debt_bills_to = 1;
        $customerSearch->contract_status = 'active';

        $debtors = $customerSearch->searchDebtors([], 0)->getModels();
        //$queryDebtors = Yii::$app->db->createCommand("SELECT SQL_CALC_FOUND_ROWS * FROM (SELECT `customer`.`customer_id`, concat(customer.lastname, ' ', customer.name) as name, `customer`.`phone`, `customer`.`code`, round(coalesce((SELECT sum(b.total * bt.multiplier) as amount FROM `bill` `b` LEFT JOIN `bill_type` `bt` ON b.bill_type_id = bt.bill_type_id WHERE b.status <> 'draft' and b.customer_id = customer.customer_id), 0) - coalesce((SELECT sum(pi.amount) FROM `payment` `p` LEFT JOIN `payment_item` `pi` ON p.payment_id = pi.payment_id and pi.payment_method_id NOT IN(SELECT `payment_method_id` FROM `payment_method` WHERE type='account') WHERE (p.status <> 'cancelled' and p.status <> 'draft') and p.customer_id = customer.customer_id), 0)) as saldo, `bills`.`debt_bills`, `bills`.`payed_bills`, ( bills.debt_bills + bills.payed_bills) as total_bills, `contract_detail`.`product_id` AS `plan`, `customer`.`company_id` AS `customer_company` FROM `customer` LEFT JOIN (SELECT customer_id, sum(qty) as debt_bills, sum(qty_2) AS payed_bills FROM ( SELECT customer_id, date, i, round(amount,2), @saldo:=round(if(customer_id<>@customer_ant and @customer_ant <> 0, amount, @saldo + amount ),2) as saldo, @customer_ant:=customer_id, if((@saldo - (select cc.percentage_tolerance_debt from customer_class_has_customer cchc INNER JOIN (SELECT customer_id, max(date_updated) maxdate FROM customer_class_has_customer GROUP BY customer_id) cchc2 ON cchc2.customer_id = cchc.customer_id AND cchc.date_updated = cchc2.maxdate LEFT JOIN customer_class cc ON cchc.customer_class_id = cc.customer_class_id where cchc.customer_id =a.customer_id)) > 0 and i=1, 1, 0) as qty, if(@saldo <= 0 AND i = 1, 1, 0) as qty_2 FROM ((SELECT `customer_id`, `b`.`date` AS `date`, if(bt.multiplier<0, 0,1) AS i, sum(b.total * bt.multiplier) AS amount FROM bill b FORCE INDEX(fk_bill_customer1_idx) LEFT JOIN `bill_type` `bt` ON b.bill_type_id = bt.bill_type_id WHERE `b`.`status` <> 'draft' GROUP BY `b`.`customer_id`, `b`.`bill_id`) UNION ALL ( SELECT `p`.`customer_id`, `p`.`date` AS `date`, 0 AS i, -p.amount FROM `payment` `p` ) ) a order by customer_id, i, date ) a GROUP BY customer_id ) `bills` ON bills.customer_id = customer.customer_id LEFT JOIN `contract` ON contract.customer_id = customer.customer_id LEFT JOIN `contract_detail` ON contract.contract_id = contract_detail.contract_id INNER JOIN `customer_class_has_customer` `cchc` ON cchc.customer_id= customer.customer_id INNER JOIN (SELECT `customer_id`, max(date_updated) maxdate FROM `customer_class_has_customer` GROUP BY `customer_id`) `cchc2` ON cchc2.customer_id = customer.customer_id and cchc.date_updated = cchc2.maxdate INNER JOIN `customer_category_has_customer` `ccathc` ON ccathc.customer_id= customer.customer_id INNER JOIN (SELECT `customer_id`, max(date_updated) maxdate FROM `customer_category_has_customer` GROUP BY `customer_id`) `ccathc2` ON ccathc2.customer_id = customer.customer_id and ccathc.date_updated = ccathc2.maxdate LEFT JOIN `customer_class` `cc` ON cchc.customer_class_id = cc.customer_class_id LEFT JOIN `customer_category` `ccat` ON ccathc.customer_category_id = ccat.customer_category_id LEFT JOIN `connection` ON connection.contract_id = contract.contract_id LEFT JOIN `node` `n` ON connection.node_id = n.node_id LEFT JOIN `company` ON company.company_id = customer.parent_company_id GROUP BY `customer`.`customer_id`, `customer`.`name`, `customer`.`phone`) `b` WHERE (`saldo` > 0) AND (`debt_bills` >= '1') AND (`debt_bills` <= '1')");
        $customers = [];

        foreach ($debtors as $debtor) {
            $customer = Customer::findOne($debtor['customer_id']);
            array_push($customers, $customer);
        }
        //        $queryCustomer = Customer::find()->andWhere(['status' => 'enabled']);


        //        if($debug) {//1403,1533,2303, 25372
        //            $queryCustomer->andWhere(new Expression( 'customer_id in (12748)'));
        //        }
        //        $customers = $queryCustomer->all();

        $this->stdout('Se les va a cortar a : ' . count($customers));
        $this->stdout("Westnet - Proceso de actualizacion de conexiones - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
        $r = 0;
        $i = 0;
        try {
            error_log("customer_id\tfacturas\tdebt_bills\tpayed_bills\tnuevo\t$\t$");

            foreach ($customers as $customer) {
                $contracts = [];

                $contracts = Contract::findAll(['customer_id' => $customer->customer_id]);

                //TODO Si el contrato tiene fecha de hoy y tiene deuda se corta
                foreach ($contracts as $contract) {
                    $connection = Connection::findOne(['contract_id' => $contract->contract_id]);

                    if ($connection) {
                        $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;

                        $estados[$connection->status_account]++;
                        if ($save) {
                            $connection->detachBehaviors();
                            $connection->update(false);
                        }
                        $i++;
                        if (($i % 1000) == 0) {
                            $this->stdout("Westnet - procesados:" . $i . "\n", Console::BOLD, Console::FG_CYAN);
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            echo $ex->getLine();
            echo $ex->getTraceAsString();
        }

        foreach ($estados as $key => $value) {
            $this->stdout("Westnet - procesados:" . $key . " - de: " . $estadosAnteriores[$key] . " a " . $value . "\n", Console::BOLD, Console::FG_BLUE);
        }
        $this->stdout("Westnet - Fin de Proceso de actualizacion de conexiones - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
    }

    /*
     * Actualiza el estado de las conexiones
     */
    private function updateConnections($save = false)
    {
        $this->stdout("Westnet - Proceso de actualizacion de conexiones - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
        $debug = false; // change to debug specific customers

        $due_day = Config::getValue('bill_due_day');
        if (!$due_day) {
            $due_day = 15;
        }
        $newContractsDays = Config::getValue('new_contracts_days');
        if (!$newContractsDays) {
            $newContractsDays = 0;
        }

        $invoice_next_month = Config::getValue('contract_days_for_invoice_next_month');

        $due_date = new \DateTime(date('Y-m-') . $due_day);
        $due_forced = null;
        $date = new \DateTime('now');
        $newContracts = new \DateTime('now +' . $newContractsDays . " days");
        $last_bill_date = new \DateTime((new \DateTime('now - 1 month'))->format('Y-m-' . $invoice_next_month));
        $bill_date = new \DateTime('last day of this month');

        $estados = [];
        $estadosAnteriores = [];

        $estadosAnteriores[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estadosAnteriores[Connection::STATUS_ACCOUNT_LOW] = 0;

        $estados[Connection::STATUS_ACCOUNT_ENABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DISABLED] = 0;
        $estados[Connection::STATUS_ACCOUNT_FORCED] = 0;
        $estados[Connection::STATUS_ACCOUNT_DEFAULTER] = 0;
        $estados[Connection::STATUS_ACCOUNT_CLIPPED] = 0;
        $estados[Connection::STATUS_ACCOUNT_LOW] = 0;

        try {
            // Traigo todos los customer para poder iterar.
            $queryCustomer = Customer::find()->where(['status' => 'enabled']);


            if ($debug) { //1403,1533,2303, 25372
                $queryCustomer->andWhere(new Expression('customer_id in (104518)'));
            }
            //$queryCustomer->orderBy(['customer_id' => SORT_DESC]); //debugging comment to sort customers differently

            $subprice = (new Query())
                ->select(['product_id', new Expression('max(date) maxdate')])
                ->from('product_price')
                ->groupBy(['product_id']);
            //$this->stdout("\nsubprice query created\n");


            $query_plan = (new Query())
                ->select(['(net_price + taxes) as price'])
                ->from(['contract c'])
                ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
                ->leftJoin('product p', 'cd.product_id = p.product_id')
                ->leftJoin('product_price pp', 'p.product_id = pp.product_id')
                ->innerJoin(['ppppim' => $subprice], 'ppppim.product_id = pp.product_id and ppppim.maxdate = pp.date')
                ->where("c.status ='active' and p.type = 'plan' and c.customer_id = :customer_id and c.contract_id = :contract_id")
                ->orderBy(['pp.date' => SORT_DESC]);
            $this->stdout("\nquery_plan query runned\n");

            $r = 0;
            $i = 0;
            // $queryCustomer->limit(100); // debug
            //$customers = $queryCustomer->limit(1000)->all(); //debug
            // $customers = $queryCustomer->all();
            // $this->stdout("\ncustomers query runned");

            // error_log("customer_id\tfacturas\tdebt_bills\tpayed_bills\tnuevo\t$\t$");
            $this->stdout("\nQuerying ".$queryCustomer->count()." (with status=>enabled)");
            $batchSize=1000;
            foreach ( $queryCustomer->batch($batchSize) as $batch_index => $customers ){
                $this->stdout("\nBatch(#$batch_index) start - current batch size (".count($customers).")");

                foreach ($customers as $customer) {
                    //$this->stdout("\n".$customer->customer_id." processing customer");
    
                    /* if($customer->customer_id == 100244){ // debug customer ID, uncomment to see in the log file if a particular customer was processed
                        $this->stdout("\n\n\n ".$customer->customer_id." is being processed----------DEBUG----------\n\n\n");
                    } */
    
                    /** @var CustomerClass $customerClass */
                    $customerClass = $customer->getCustomerClass()->one();
    
                    if(!isset($customerClass) or empty($customerClass)){ // if the previous query doesnt return anything, its probably a case of a customer created for testing. so we set a default value for it.
                        $this->stdout("\n".$customer->customer_id." (id) didn't had any CustomerClass - Setting it a new one.");
                        //break;
                        $customer->setCustomerClass(5);
                        $customerClass = $customer->getCustomerClass(); // get it again.
                    }
    
                    $aviso_date = clone $due_date;
                    $cortado_date = clone $due_date;
                    $aviso_date->modify('+' . $customerClass->tolerance_days . ' day');
                    $cortado_date->modify('+' . $customerClass->days_duration . ' day');
    
                    $contracts = [];
    
                    // Si no tiene deuda o la deuda es menor a la tolerancia, habilito.
                    $payment = new Payment();
                    $payment->customer_id = $customer->customer_id;
                    $amount = round($payment->accountTotal());
                    $contracts = Contract::findAll(['customer_id' => $customer->customer_id]);
    
                    //TODO Si el contrato tiene fecha de hoy y tiene deuda se corta
                    foreach ($contracts as $contract) {
                        $connection = Connection::findOne(['contract_id' => $contract->contract_id]);
    
                        $precio_plan = clone $query_plan;
    
                        $precioPlan = $query_plan->addParams([
                            ':customer_id' => $contract->customer_id,
                            ':contract_id' => $contract->contract_id
                        ])->scalar();
    
                        try {
                            $from_date = new \DateTime(($contract->from_date ? $contract->from_date : $contract->date));
                        } catch (\Exception $ex) {
                            continue;
                        }
    
                        if ($connection) {
                            $status_old = $connection->status_account;
                            if (is_null($status_old)) {
                                $status_old = $connection->status_account = Connection::STATUS_ACCOUNT_DISABLED;
                            }
                            $estadosAnteriores[$connection->status_account]++;
    
                            // if status_account comes disabled or is already, do:
                            if ($connection->status_account == Connection::STATUS_ACCOUNT_DISABLED) {
                                // return $connection->status_account; // if inside cronjob, logic is different so no return is used. instead use continue; and count++
                                $estados[$connection->status_account]++;
                                continue;
                            }
    
                            // Si la conexion esta forzada,
                            // En el caso de que la fecha de forzado sea mayor a hoy, proceso normalmente, buscando deuda
                            // y demas.
                            if ($connection->status_account == Connection::STATUS_ACCOUNT_FORCED) {
                                $due_forced = $date;
                                try {
                                    $due_forced = new \DateTime($connection->due_date);
                                } catch (\Exception $ex) {
                                    $connection->due_date = null;
                                }
                                // Si la fecha de forzado es mayor a hoy, es porque todavia no se cumple y lo tengo que omitir
                                if ($due_forced >= $date) {
                                    $estados[$connection->status_account]++;
                                    continue;
                                }
                            }
    
    
                            $debtLastBill = $this->debtLastBill($customer->customer_id);
    
                            //$bills = $this->getBills($customer->customer_id);
    
                            $newContractsFromDate = clone $from_date;
                            $newContractsFromDate->modify('+' . $newContractsDays . " days");
    
                            if ($debug) {
                                error_log(
                                    "\n - customer_id " . $contract->customer_id . " " .
                                    "\n - from: " . $from_date->format('Y-m-d') . " - newContracts: " . $newContracts->format('Y-m-d') .
                                    "\n - newContractsFromDate: " . $newContractsFromDate->format('Y-m-d') .
                                    "\n - aviso_date: " . $aviso_date->format('Y-m-d') .
                                    "\n - cortado_date: " . $cortado_date->format('Y-m-d') .
                                    "\n - due_date: " . $due_date->format('Y-m-d') .
                                    "\n - due_forced: " . ($due_forced ? $due_forced->format('Y-m-d') : '') .
                                    "\n - amount: " . $amount . " - tolerancia: " . $customerClass->percentage_tolerance_debt .
                                    "\n - debtLastBill: " . $debtLastBill .
                                    "\n - days: " . $date->diff($from_date)->days . " - newContractsDays: " . $newContractsDays
                                );
                            }
    
                            // Si no esta en proceso de baja
                            if (
                                $contract->status != Contract::STATUS_LOW_PROCESS &&
                                $contract->status != Contract::STATUS_LOW &&
                                $connection->status_account != Connection::STATUS_ACCOUNT_LOW
                            ) {
                                /** Habilito
                                 *  - es free o
                                 *  - No tiene deuda o
                                 *  - Tiene deuda menor al porcentaje de tolerancia y hoy es menor a la fecha de corte y menor a la fecha de corte por nuevo y debe una o menos facturas
                                 *
                                 */
                                $tiene_deuda = ($amount < 0); // amount (account balance) is calculated in runtime (resource extensive)
                                $tiene_deuda_sobre_tolerante = (round(abs($amount)) >= $customerClass->percentage_tolerance_debt); // BEWARE, even if 'percentage' is written, here is using the integer value. not a percentage
                                $es_nueva_instalacion = ($date->diff($from_date)->days <= $newContractsDays);
                                $avisa = ($date >= $aviso_date && $date < $cortado_date);
                                $corta = ($date >= $cortado_date);
                                $es_nuevo = ($from_date >= $last_bill_date && $from_date <= $bill_date);
                                //Verificamos que la ultima factura cerrada sea del mes corriente.
                                $last_closed_bill = $customer->getLastClosedBill();
                                $last_closed_bill_date = $last_closed_bill ? (new \DateTime($last_closed_bill->date)) : false;
                                $lastBillItsFromActualMonth = $last_closed_bill_date ? ($date->format('Y-m') == $last_closed_bill_date->format('Y-m')) : true;
    
                                if ($debug) {
                                    error_log(
                                        "\n - " . 'tiene_deuda: ' . ($tiene_deuda ? 's' : 'n') .
                                        "\n - " . 'tiene_deuda_sobre_tolerante: ' . ($tiene_deuda_sobre_tolerante ? 's' : 'n') .
                                        "\n - " . 'es_nueva_instalacion: ' . ($es_nueva_instalacion ? 's' : 'n') .
                                        "\n - " . 'avisa: ' . ($avisa ? 's' : 'n') .
                                        "\n - " . 'corta: ' . ($corta ? 's' : 'n') .
                                        "\n - " . 'es_nuevo: ' . ($es_nuevo ? 's' : 'n') .
                                        "\n - " . 'last_bill_date: ' . $last_bill_date->format('Y-m-d'));
                                }
    
    
                                if (strtolower($customerClass->name) == 'free') {
                                    $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                                } else if ($es_nueva_instalacion) {
                                    $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                                } else if ($es_nuevo && $tiene_deuda && $tiene_deuda_sobre_tolerante) {
                                    $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;
                                    //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill['debt_bills'] . "\t" .$debtLastBill2['payed_bills'] . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );
                                    //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill  . "\t" .$debtLastBill  . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );
    
                                } else if ($connection->status_account == Connection::STATUS_ACCOUNT_CLIPPED) {
                                    /**
                                     * Habilito si:
                                     *  - solo debe la factura del mes actual y la fecha es menor a la de corte
                                     */
                                    if (
                                        !$tiene_deuda || ($tiene_deuda && !$tiene_deuda_sobre_tolerante) || 
                                        (!($debtLastBill > 1) && !$corta && $lastBillItsFromActualMonth && $date < $cortado_date)
                                        ) {
                                        $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                                    }
                                } else if (
                                    (!$tiene_deuda ||
                                        ($tiene_deuda && !$tiene_deuda_sobre_tolerante) ||
                                        ($tiene_deuda && $tiene_deuda_sobre_tolerante && $debtLastBill <= 1 && $lastBillItsFromActualMonth && !$corta && !$avisa)
                                    )
                                ) {
                                    $connection->status_account = Connection::STATUS_ACCOUNT_ENABLED;
                                } else if (($tiene_deuda && $tiene_deuda_sobre_tolerante) && $avisa && $debtLastBill <= 1) {
                                    /**
                                     * Deudor:
                                     *  -  No esta en proceso de baja
                                     *  -  Tiene deuda mayor a la tolerancia.
                                     *  -  Hoy es mayor a la fecha de aviso y menor a la de corte o
                                     *      - hoy es menor a la de aviso y menor a la de corte y debe mas de una factura
                                     */
                                    $connection->status_account = Connection::STATUS_ACCOUNT_DEFAULTER;
                                } else if (
                                    (($tiene_deuda && $tiene_deuda_sobre_tolerante) &&
                                        ($corta && $debtLastBill >= 1) ||
                                        ($debtLastBill >= 1 && !$es_nueva_instalacion)) &&
                                    (($connection->status_account == Connection::STATUS_ACCOUNT_FORCED &&  ($due_date && $due_forced ? $date > $due_forced : false)) || $connection->status_account != Connection::STATUS_ACCOUNT_FORCED) ||
                                    $connection->status_account == Connection::STATUS_ACCOUNT_CLIPPED
                                ) {
                                    /**
                                     * Cortado
                                     *  -  Si tiene deuda mayor a la tolerancia y
                                     *      -  debe una o mas facturas y
                                     *      -  hoy es mayor a la fecha de aviso y menor a la fecha de corte
                                     *  -  Esta forzado y hoy es mayor a la fecha de forzado
                                     *  -  No esta en proceso de baja
                                     */
                                    $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;
                                    //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill  . "\t" .$debtLastBill  . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );
                                    //error_log( $contract->customer_id . "\t" . $bills ."\t". $debtLastBill2['debt_bills'] . "\t" .$debtLastBill2['payed_bills'] . "\t" .$debtLastBill . "\t" . $amount . "\t" . ceil($precioPlan) );
                                }
                            }
    
                            $estados[$connection->status_account]++;
                            if ($save) {
                                if ($status_old != $connection->status_account) {
                                    $connection->detachBehaviors();
                                    $connection->update(false);
                                }
                            }
                            $i++;
                            if (($i % 1000) == 0) {
                                $this->stdout("\nWestnet - procesados:" . $i, Console::BOLD, Console::FG_CYAN);
                            }
                        }
                    }
                }
            }

            //$this->stdout(var_dump($estadosAnteriores,$estados)); // only for debugging
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            echo $ex->getLine();
            echo $ex->getTraceAsString();
            $this->stdout("\nProcess Finished Unexpectedly.");

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Finished Unexpectedly: connection-status/update ****', $ex);
        }
        
        $this->stdout("\n----Summary----\n");
        foreach ($estados as $key => $value) {
            $this->stdout("Westnet - procesados:" . $key . " - de: " . $estadosAnteriores[$key] . " a " . $value . "\n", Console::BOLD, Console::FG_BLUE);
        }
        $this->stdout("Westnet - Fin de Proceso de actualizacion de conexiones - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
    }

    public function updatePlans($date = null, $from_date = null, $limit = null)
    {
        $this->stdout("Westnet - Proceso de actualizacion de planes - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);

        if ($date == null || $date == 'null') {
            $date = (new \DateTime('now'))->format('Y-m-d');
        }

        $subQuery = (new Query())
            ->select(['product_id'])
            ->from(['contract_detail_log cdl'])
            ->where('cdl.contract_detail_id = cd.contract_detail_id')
            ->orderBy(['contract_detail_log_id' => SORT_DESC])
            ->limit(1);

        $query = (new Query())
            ->select([
                'c.customer_id', 'c.contract_id', 'cus.code',  'cd.contract_detail_id', 'c.external_id',
                'p.system', 's.server_id', 's.url', 's.token', new Expression('inet_ntoa(ip4_1) as ip')
            ])
            ->from('contract c')
            ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
            ->leftJoin('customer cus', 'c.customer_id = cus.customer_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('connection con', 'c.contract_id = con.contract_id')
            ->leftJoin('server s', 'con.server_id = s.server_id')
            ->andWhere(['c.status' => Contract::STATUS_ACTIVE])
            ->andWhere(['IS NOT', 's.server_id', NULL])
            ->andWhere(['p.type' => 'plan', 'cd.applied' => 0])
            ->andWhere(['<=', 'cd.from_date', $date])
            ->andFilterWhere(['>=', 'cd.from_date', $from_date])
            //->andWhere(['<>', 'cd.product_id', $subQuery])
        ;

        if (!empty($limit)) {
            $query->limit($limit);
        }

        echo $query->createCommand()->getRawSql();

        $contracts = $query->all();

        $contractRequests = [];
        $planes = [];
        $apis = [];
        foreach (Server::find()->all() as $server) {
            $apis[$server->server_id] = IspFactory::getInstance()->getIsp($server);
        }

        $iguales = 0;
        $distintos = 0;
        try {
            $wispro = new SecureConnectionUpdate();

            foreach ($contracts as $contractRes) {
                if (array_key_exists($contractRes['server_id'], $contractRequests) === false) {
                    $contractRequests[$contractRes['server_id']] = $apis[$contractRes['server_id']]->getContractApi();
                }
                if (array_key_exists($contractRes['server_id'] . "_" . $contractRes['system'],  $planes) === false) {
                    $plansRequest = $apis[$contractRes['server_id']]->getPlanApi();
                    $plans = $plansRequest->listAll();
                    if (is_array($plans)) {
                        foreach ($plans as $plan) {
                            $planes[$contractRes['server_id'] . "_" . preg_replace("[ |/]", "-", strtolower($plan['plan']['name']))] = $plan['plan']['id'];
                        }
                    } else {
                        $this->stdout("Error al traer contratos del server " . $contractRes['server_id']);
                    }
                }

                $contractRequest =  $contractRequests[$contractRes['server_id']];

                // Verifico que exista el plan
                if (array_key_exists($contractRes['server_id'] . "_" . $contractRes['system'], $planes) !== false) {

                    $plan_id = (string)$planes[$contractRes['server_id'] . "_" . $contractRes['system']];

                    $contract = Contract::findOne(['contract_id' => $contractRes['contract_id']]);
                    $connection = Connection::findOne(['contract_id' => $contractRes['contract_id']]);

                    $this->stdout("Buscando contrato: " . $contractRes['contract_id'] . " - Customer: " . $contractRes['customer_id'] . "\n", Console::BOLD, Console::FG_RED);

                    $contractRest = new \app\modules\westnet\isp\models\Contract($contract, $connection, $plan_id);
                    try {
                        // Busco el contrato por IP
                        $contract_api = $contractRequest->find($contractRest->ip, ContractRequest::Q_IP);
                        $contract_api = is_array($contract_api) ? $contract_api[0] : null;
                        if ($contract_api !== null) {
                            if ($contract_api->plan_id == $plan_id) {
                                $iguales++;
                                $contractDetail = ContractDetail::findOne(['contract_detail_id' => $contractRes['contract_detail_id']]);
                                $contractDetail->updateAttributes(['applied' => 1]);
                            } else {
                                $this->stdout("Customer code: " . $contractRes['code'] . " - " . $contract_api->plan_id . "=> " . $plan_id . " - " . $contractRes['external_id'] . " - " . $contractRest->client_id . "\n", Console::BOLD, Console::FG_GREEN);
                                if ($contract_api->id != $contract->external_id) {
                                    $contract->external_id = $contract_api->id;
                                    $this->stdout("actualizo external_id: " . $contract_api->id);
                                    $contract->updateAttributes(['external_id']);
                                }

                                $contractDetail = ContractDetail::findOne(['contract_detail_id' => $contractRes['contract_detail_id']]);
                                $contractDetail->applied = true;
                                $contractDetail->updateAttributes(['applied']);

                                $wispro->update($connection, $contract, true);
                                $distintos++;
                            }
                        } else {
                            $this->stdout("No Encontrado. Server: " . $contractRes['server_id'] . " - Customer: " . $contractRes['customer_id'] . "\n", Console::BOLD, Console::FG_RED);
                        }
                    } catch (\Exception $ex) {
                        $this->stdout("Exepcion.\n", Console::BOLD, Console::FG_RED);
                        $this->stdout($ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine(), Console::BOLD, Console::FG_RED);
                        $this->stdout($ex->getTraceAsString(), Console::BOLD, Console::FG_RED);
                        
                        // send error to telegram
                        TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: connection-status/update-plan ****', $ex);
                    }
                } else {
                    $this->stdout("Plan No Encontrado: " . $contractRes['customer_id'] . " - " . $contractRes['server_id'] . "_" . $contractRes['system'] . "\n", Console::BOLD, Console::FG_RED);
                }
            }
        } catch (\Exception $ex) {
            $this->stdout("Exepcion.\n", Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine(), Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getTraceAsString(), Console::BOLD, Console::FG_RED);

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: connection-status/update-plan ****', $ex);
        }

        $this->stdout("Totales" . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Iguales: " . $iguales . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Distintos: " . $distintos . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("-----------------------------------------------------" . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
    }

    public function corregirPlanes($limit = null, $server_id = null, $plan_id = null)
    {
        $this->stdout("Westnet - Proceso de correción de planes - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Westnet - Proceso de correción de planes - " . (new \DateTime())->format('d-m-Y H:i:s') . "\n", FILE_APPEND);

        $date = (new \DateTime('now'))->format('Y-m-d');

        $subQuery = (new Query())
            ->select(['product_id'])
            ->from(['contract_detail_log cdl'])
            ->where('cdl.contract_detail_id = cd.contract_detail_id')
            ->orderBy(['contract_detail_log_id' => SORT_DESC])
            ->limit(1);

        $query = (new Query())
            ->select([
                'c.customer_id', 'c.contract_id', 'cus.code',  'cd.contract_detail_id', 'c.external_id', 'p.name as plan',
                'p.system', 's.server_id', 's.name as server', 's.url', 's.token', new Expression('inet_ntoa(ip4_1) as ip')
            ])
            ->from('contract c')
            ->leftJoin('contract_detail cd', 'c.contract_id = cd.contract_id')
            ->leftJoin('customer cus', 'c.customer_id = cus.customer_id')
            ->leftJoin('product p', 'cd.product_id = p.product_id')
            ->leftJoin('connection con', 'c.contract_id = con.contract_id')
            ->leftJoin('server s', 'con.server_id = s.server_id')
            ->andWhere(['IN', 'c.status', [Contract::STATUS_ACTIVE, Contract::STATUS_LOW_PROCESS]])
            ->andWhere(['IS NOT', 's.server_id', NULL])
            //->andWhere(['p.type' => 'plan', 'cd.applied' => 0])
            ->andWhere(['<=', 'cd.from_date', $date])
            //->andWhere(['<>', 'cd.product_id', $subQuery])
        ;

        if (is_array($server_id)) {
            $query->andFilterWhere(['IN', 's.server_id', $server_id]);
        } else {
            $query->andFilterWhere(['s.server_id' => $server_id]);
        }

        if (is_array($plan_id)) {
            $query->andFilterWhere(['IN', 'p.product_id', $plan_id]);
        } else {
            $query->andFilterWhere(['p.product_id' => $plan_id]);
        }

        if (!empty($limit)) {
            $query->limit($limit);
        }

        echo $query->createCommand()->getRawSql();

        $contracts = $query->all();

        $this->stdout('Contratos encontrados: ' . count($contracts) . "\n");

        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Contratos encontrados: ' . count($contracts) . "\n", FILE_APPEND);

        $contractRequests = [];
        $planes = [];
        $apis = [];
        foreach (Server::find()->all() as $server) {
            $apis[$server->server_id] = IspFactory::getInstance()->getIsp($server);
        }

        $iguales = 0;
        $distintos = 0;
        $errors = 0;
        try {
            $wispro = new SecureConnectionUpdate();

            foreach ($contracts as $contractRes) {
                if (array_key_exists($contractRes['server_id'], $contractRequests) === false) {
                    $contractRequests[$contractRes['server_id']] = $apis[$contractRes['server_id']]->getContractApi();
                }
                if (array_key_exists($contractRes['server_id'] . "_" . $contractRes['system'],  $planes) === false) {
                    $plansRequest = $apis[$contractRes['server_id']]->getPlanApi();
                    $plans = $plansRequest->listAll();
                    if (is_array($plans)) {
                        foreach ($plans as $plan) {
                            $planes[$contractRes['server_id'] . "_" . preg_replace("[ |/]", "-", strtolower($plan['plan']['name']))] = $plan['plan']['id'];
                        }
                    } else {
                        $this->stdout("Error al traer contratos del server " . $contractRes['server_id']);
                        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Error al traer contratos del server " . $contractRes['server'] . "\n", FILE_APPEND);
                        $errors++;
                    }
                }

                $contractRequest =  $contractRequests[$contractRes['server_id']];

                // Verifico que exista el plan
                if (array_key_exists($contractRes['server_id'] . "_" . $contractRes['system'], $planes) !== false) {

                    $plan_id = (string)$planes[$contractRes['server_id'] . "_" . $contractRes['system']];

                    $contract = Contract::findOne(['contract_id' => $contractRes['contract_id']]);
                    $connection = Connection::findOne(['contract_id' => $contractRes['contract_id']]);

                    $this->stdout("Buscando contrato: " . $contractRes['contract_id'] . " - Customer: " . $contractRes['customer_id'] . "\n", Console::BOLD, Console::FG_RED);
                    file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Buscando contrato: " . $contractRes['contract_id'] . " en ISP " . $contractRes['server'] . " - Customer: " . $contractRes['code'] . "\n", FILE_APPEND);

                    $contractRest = new \app\modules\westnet\isp\models\Contract($contract, $connection, $plan_id);
                    try {
                        // Busco el contrato por IP
                        $contract_api = $contractRequest->find($contractRest->ip, ContractRequest::Q_IP);
                        $contract_api = is_array($contract_api) ? $contract_api[0] : null;
                        if ($contract_api !== null) {
                            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Contrato Encontrado', FILE_APPEND);
                            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Plan en Gestion: ' . $contractRes['system'] . "\n", FILE_APPEND);
                            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Plan en ISP ' . $contract_api->plan_id . "\n", FILE_APPEND);

                            if ($contract_api->plan_id == $plan_id) {
                                file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Plan correcto en ambos lados. No realizo cambio' . "\n", FILE_APPEND);
                                $iguales++;
                                $contractDetail = ContractDetail::findOne(['contract_detail_id' => $contractRes['contract_detail_id']]);
                                $contractDetail->updateAttributes(['applied' => 1]);
                            } else {
                                $this->stdout("Customer code: " . $contractRes['code'] . " - " . $contract_api->plan_id . "=> " . $plan_id . " - " . $contractRes['external_id'] . " - " . $contractRest->client_id . "\n", Console::BOLD, Console::FG_GREEN);
                                file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Cambio plan de ' .  $contract_api->plan_id . ' a ' . $plan_id . "\n", FILE_APPEND);
                                if ($contract_api->id != $contract->external_id) {
                                    $contract->external_id = $contract_api->id;
                                    $this->stdout("actualizo external_id: " . $contract_api->id);
                                    $contract->updateAttributes(['external_id']);
                                }

                                $contractDetail = ContractDetail::findOne(['contract_detail_id' => $contractRes['contract_detail_id']]);
                                $contractDetail->applied = true;
                                $contractDetail->updateAttributes(['applied']);

                                $result = $wispro->update($connection, $contract, true);

                                if ($result) {
                                    file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Plan cambiado con éxito' . "\n", FILE_APPEND);
                                    $distintos++;
                                } else {
                                    file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), 'Error durante la comunicacion con isp' . "\n", FILE_APPEND);
                                    $errors++;
                                }
                            }
                        } else {
                            $this->stdout("No Encontrado. Server: " . $contractRes['server_id'] . " - Customer: " . $contractRes['customer_id'] . "\n", Console::BOLD, Console::FG_RED);
                            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "No Encontrado. Server: " . $contractRes['server_id'] . " - Customer: " . $contractRes['customer_id'] . "\n", FILE_APPEND);

                            $errors++;
                        }
                    } catch (\Exception $ex) {
                        $this->stdout("Exepcion.\n", Console::BOLD, Console::FG_RED);
                        $this->stdout($ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine(), Console::BOLD, Console::FG_RED);
                        $this->stdout($ex->getTraceAsString(), Console::BOLD, Console::FG_RED);
                    }
                } else {
                    $this->stdout("Plan No Encontrado: " . $contractRes['customer_id'] . " - " . $contractRes['server_id'] . "_" . $contractRes['system'] . "\n", Console::BOLD, Console::FG_RED);
                    file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Plan No Encontrado: " . $contractRes['customer_id'] . " - " . $contractRes['server_id'] . "_" . $contractRes['system'] . "\n", FILE_APPEND);

                    $errors++;
                }
                file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "----------------------------------------------------------------------------------------------" . "\n", FILE_APPEND);
            }
        } catch (\Exception $ex) {
            $this->stdout("Exepcion.\n", Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine(), Console::BOLD, Console::FG_RED);
            $this->stdout($ex->getTraceAsString(), Console::BOLD, Console::FG_RED);
            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Exepcion.\n", FILE_APPEND);
            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), $ex->getMessage() . " - " . $ex->getFile() . " - " . $ex->getLine(), FILE_APPEND);
            file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), $ex->getTraceAsString() . "\n", FILE_APPEND);
        }

        $this->stdout("Totales" . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Iguales: " . $iguales . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Distintos: " . $distintos . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("Erroneos: " . $errors . "\n", Console::BOLD, Console::FG_RED);
        $this->stdout("-----------------------------------------------------" . (new \DateTime())->format('d-m-Y H:i:s') . "\n", Console::BOLD, Console::FG_CYAN);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Totales" . "\n", FILE_APPEND);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Iguales: " . $iguales . "\n", FILE_APPEND);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Distintos: " . $distintos . "\n", FILE_APPEND);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "Erroneos: " . $errors . "\n", FILE_APPEND);
        file_put_contents(Yii::getAlias('@runtime/logs/correcion_planes_log.txt'), "-----------------------------------------------------" . (new \DateTime())->format('d-m-Y H:i:s') . "\n", FILE_APPEND);
    }
}
