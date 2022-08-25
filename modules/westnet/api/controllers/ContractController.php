<?php

namespace app\modules\westnet\api\controllers;

use app\components\web\RestController;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class ContractController extends RestController
{
    public $modelClass = 'app\modules\sale\modules\contract\models\Contract';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['index']);
        
        return $actions;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD', 'POST'],
        ];
    }

    /**
     * Retorna el contrato segun el id pasado como parametro.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $response = [
            'status' => 'error',
            'message' => Yii::t('app', 'No contract id specified.'),
            'contracts' => []
        ];

        $post = Yii::$app->request->post();

        $customer_id = (isset($post['customer_id']) ?  $post['customer_id'] : null);
        $id = (isset($post['id']) ?  $post['id'] : null);
        $customer_name = (isset($post['customer_name']) ?  $post['customer_name'] : null);

        if($id || $customer_id || $customer_name) {
            // Busco el contrato.
            if($id) {
                $contracts = Contract::findAll(['contract_id'=>$id]);
            } elseif($customer_id) {
                // here it enters when creating a ticket inside mesa when body is smt like: "customer_id" : "28211" (example)
                $contracts = Contract::find()
                    ->leftJoin('customer', 'contract.customer_id = customer.customer_id')
                    ->andWhere(['customer.code'=>$customer_id])
                    ->orderBy(['contract.status'=>SORT_ASC]) // added this sort that helps all ACTIVE contracts to be above the disabled ones.
                    ->all();
            } elseif($customer_name){
                $searchHelper = new \app\components\helpers\SearchStringHelper();
                $searchHelper->string = $customer_name;

                //Separamos las palabras de busqueda
                $words = $searchHelper->getSearchWords('%{word}%');

                $operator = 'like';

                $query = Contract::find()
                    ->leftJoin('customer', 'contract.customer_id = customer.customer_id')
                    ->orderBy('customer.customer_id')
                ;
                $where = "";
                foreach ($words as $word) {
                    $where .= " (customer.name LIKE '".$word."' OR customer.lastname LIKE '".$word."') AND ";
                }
                $query->where(substr ($where, 0, strlen($where)-4));

                $contracts = $query->all();
            }

            if($contracts == null || empty($contracts)) {
                $response['message'] = Yii::t('app', 'The contract does not exist.');
            } else {
                $response = [
                    'status' => 'success',
                    'message' => '',
                    'contracts' => []
                ];

                $old_customer_id = null;
                // Armo la base de la respuesta
                $connections = [];
                $contractData = [];
                foreach($contracts as $key => $contract) {

                    if($contract->customer_id != $old_customer_id && $old_customer_id != null) {
                        $contractData['connections'] = $connections;
                        $response['contracts'][] = $contractData;
                        $contractData = [];
                        $connections = [];
                    }

                    $connection = Connection::findOne(['contract_id' => $contract->contract_id]);

                    if(empty($contractData['customer_name'])) {
                        $customer = $contract->customer;
                        // Busco la deuda
                        $searchModel = new PaymentSearch();
                        $searchModel->customer_id = $customer->customer_id;

                        $contractData = [
                            'customer_code' => $customer->code,
                            'payment_code' => $customer->payment_code,
                            'customer_name' => $customer->name,
                            'customer_lastname' => $customer->lastname,
                            'customer_description' => $customer->description,
                            'due' => $searchModel->accountTotal(),
                            'company'           => [
                                'company_id'    => ($customer->parentCompany ? $customer->parentCompany->company_id : $customer->company_id ),
                                'name'          => ($customer->parentCompany ? $customer->parentCompany->name : $customer->company->name ),
                            ]
                        ];
                    }

                    $con = [
                        'contract_id' => $contract->contract_id,
                        'ip' => ($connection ? $connection->getIp41Formatted(): '0.0.0.0'),
                        'ip_2' => ($connection ? $connection->getIp42Formatted(): '0.0.0.0'),
                        'account_status' => ($connection ? $connection->status_account : ''),
                        'date' => $contract->from_date,
                        'plan' => $contract->getPlan()->name,
                        'address' => ($contract->address ? $contract->address->getFullAddress() : ($customer->address ? $customer->address->getFullAddress(): '') ),
                        'geocode' => (!empty($contract->address->geocode) ? $contract->address->geocode : null),
                        'node' => ($connection ? ($connection->node ? $connection->node->subnet : '' )  : ''),
                        'server' => ($connection ? ($connection->server ? $connection->server->name : '' ) : ''),
                        
                    ];
                    $features = $contract->getPlan()->getPlanFeatures()->all();
                    foreach($features as $feature) {
                        $con[$feature->parent->name] = (int)$feature->name;
                    }
                    $connections[] = $con;

                    $old_customer_id = $contract->customer_id;
                }
                if($old_customer_id != null) {
                    $contractData['connections'] = $connections;
                    $response['contracts'][] = $contractData;
                }
            }
        }

        return $response;
    }

    /*
     * Lista los contratos que son enviados por parametro
     * @return array
     */
    public function actionListById()
    {
        $post = Yii::$app->request->post();

        $response = [];

        $contracts = Contract::find()->andWhere(['in','contract_id', explode(',', $post['id'])])->all();

        if($contracts !== null || !empty($contracts)) {
            // Armo la base de la respuesta
            $connections = [];
            foreach($contracts as $contract) {
                $connection = Connection::findOne(['contract_id' => $contract->contract_id]);
                $customer = $contract->customer;
                $response[] = [
                    'contract_id' => $contract->contract_id,
                    'ip' => ($connection ? $connection->getIp41Formatted() : '0.0.0.0'),
                    'ip_2' => ($connection ? $connection->getIp42Formatted() : '0.0.0.0'),
                    'account_status' => ($connection ? $connection->status_account : ''),
                    'address' => ($contract->address ? $contract->address->getFullAddress() : ($contract->customer->address ? $contract->customer->address->getFullAddress(): '') ),
                    'geocode' => (!empty($contract->address->geocode) ? $contract->address->geocode : null),
                    'date' => $contract->from_date,
                    'plan' => $contract->getPlan()->name,
                    'node' => ($connection ? ($connection->node ? $connection->node->subnet : '' )  : ''),
                    'tentative_node' => $contract->tentative_node,
                    'instalation_schedule' => $contract->instalation_schedule,
                    'server' => ($connection ? ($connection->server ? $connection->server->name : '' ) : ''),
                    'company'           => [
                        'company_id'    => ($customer->parentCompany ? $customer->parentCompany->company_id : $customer->company_id ),
                        'name'          => ($customer->parentCompany ? $customer->parentCompany->name : $customer->company->name ),
                    ]
                ];
                $features = $contract->getPlan()->getPlanFeatures()->all();
                foreach($features as $feature) {
                    $response[$feature->parent->name] = (int)$feature->name;
                }

            }
        }

        return $response;
    }

    /**
     * Lista los contratos que tienen aviso de mora o corte con aviso.
     * @return array
     */
    public function actionMora()
    {
        $response = [];

        $multiple = (property_exists(Customer::className(), 'parent_company_id'));

        $query = (new Query())
            ->select(['contract.contract_id','contract.customer_id','cus.name', 'cus.code as customer_code', 'cus.payment_code',
                'con.contract_id', new Expression('inet_ntoa(con.ip4_1) as ip'),
                new Expression('inet_ntoa(con.ip4_2) as ip_2'),
                'con.status_account as account_status', 'c.company_id', 'c.name as company_name'
            ])
            ->from('contract')
            ->leftJoin('customer cus', 'cus.customer_id = contract.customer_id')
            ->leftJoin('connection as con', 'contract.contract_id = con.contract_id')
            ->andWhere(['in', 'con.status_account', ['defaulter','clipped','disabled']])
            ->andWhere(['in', 'contract.status', ['active','low-process']])
            ->andWhere([ // an error was encountered that was relationed with the portal cautivo that warned customers. some customers where missing due to this extra filters
                'cus.status' => Customer::STATUS_ENABLED,
                'con.status' => Connection::STATUS_ENABLED
            ])
        ;

        if($multiple) {
            $query->leftJoin('company c', 'c.company_id = coalesce(cus.parent_company_id, cus.company_id)');
        } else {
            $query->leftJoin('company c', 'c.company_id = cus.company_id');
        }

        //var_dump($query->all());die();
        $contracts = $query->all();

        $searchModel = new PaymentSearch();
        foreach($contracts as $contract) {
            $searchModel->customer_id = $contract['customer_id'];
            unset($contract['customer_id']); // the Customer ID is searched in the query beforehand just to calculate its accountTotal *amount. Then is unsetted
            $response[] = array_merge($contract, [
                'due' => $searchModel->accountTotal()
            ]);
        }
        
        return $response;
    }

    /**
     * Lista los contratos que tienen aviso de mora o corte con aviso. 
     * Modified to skip the foreach loop, which gives a more performant api. The DUE field is updated by a cronjob , making it really innefective.
     * @return array
     */
    public function actionMoraV2()
    {
        $response = [];

        $multiple = (property_exists(Customer::className(), 'parent_company_id'));

        $query = (new Query())
            ->select([
                'contract.contract_id','contract.customer_id',
                'cus.name', 'cus.code as customer_code', 'cus.payment_code',
                'con.contract_id', 
                new Expression('inet_ntoa(con.ip4_1) as ip'), // inet_ntoa transforms int into IP format (0.0.0.0)
                new Expression('inet_ntoa(con.ip4_2) as ip_2'),
                'con.status_account as account_status', 'c.company_id', 'c.name as company_name',
                'IFNULL(cus.current_account_balance,0) AS due' // IFNULL saves us time converting  NULL values into 0.
            ])
            ->from('contract')
            ->leftJoin('customer cus', 'cus.customer_id = contract.customer_id')
            ->leftJoin('connection as con', 'contract.contract_id = con.contract_id')

            ->andWhere(['in', 'con.status_account', ['defaulter','clipped','disabled']]) // combinatorial of "Estado de Conexion"
            ->andWhere(['in', 'contract.status', ['active','low-process','low']]) // combinatorial of "Estados de Contrato"
            ->andWhere(['!=','con.ip4_1','0']) // added not to give IP values that are 0. So as not to break anything when consuming the endpoint
        ;

        if($multiple) {
            $query->leftJoin('company c', 'c.company_id = coalesce(cus.parent_company_id, cus.company_id)');
        } else {
            $query->leftJoin('company c', 'c.company_id = cus.company_id');
        }

        $contracts = $query->all();
        
        return $contracts;
    }

    /**
     * Makes an array of contracts that are defaulters or should be clipped.
     * Also changed the data that is returned from database to all contracts !='active' 
     * and found that it does not suffice because we also need some internal groups of Active 
     * contracts to be defaulter/clipped off.
     * @return array
     */
    public function actionMoraV3()
    {
        $contractsArr = array(); // this is the array of contracts that should be returned. Portal captivo uses it to determine clipped and defaulters
        
        // SELECT ATTRIBUTES FOR THE SUBQUERY
        $selectArr = [
            'co.contract_id',
            'co.customer_id',
            'UPPER(TRIM(cus.name)) AS name',
            'UPPER(TRIM(cus.lastname)) AS lastname',
            'cus.code AS customer_code',
            'cus.payment_code',
            'inet_ntoa(con.ip4_1)',
            'inet_ntoa(con.ip4_1_old)',
            'CASE
                WHEN (
                    con.ip4_1 IS NULL
                    OR con.ip4_1 = 0
                ) THEN inet_ntoa(con.ip4_1_old)
                ELSE inet_ntoa(con.ip4_1)
            END AS ip',
            'inet_ntoa(con.ip4_2) AS ip_2',
            'con.status_account AS account_status',
            'c.company_id',
            'c.name AS company_name',
            'IFNULL(cus.current_account_balance, 0) AS due'
        ];

        $subQuery = (new Query())
            // SELECT ATTRIBUTES
            ->select($selectArr)
            ->from('contract co')
            // DEFAULT JOINS
            ->leftJoin('customer cus', 'cus.customer_id = co.customer_id')
            ->leftJoin('connection as con', 'co.contract_id = con.contract_id')
            // WHERE CONDITIONS
            ->where(['and',
                ['in', 'con.status_account', ['defaulter','clipped','disabled','low']],
                ['in','co.status',['active']]
            ])
            ->orWhere(
                ['not in','co.status',['active']]
            )
            ;

        // CONDITIONAL JOINS
        $multiple = (property_exists(Customer::className(), 'parent_company_id'));
        if($multiple) {
            $subQuery->leftJoin('company c', 'c.company_id = coalesce(cus.parent_company_id, cus.company_id)');
        } else {
            $subQuery->leftJoin('company c', 'c.company_id = cus.company_id');
        }
        // at this point we have the subquery built, but we have to remove all IPs which are zero //testing. 54213 reg
        
        // BUILD MAIN QUERY
        $query = (new Query())
                ->select(['ip.*'])
                ->from(['ip'=>$subQuery])
                ->where(['!=','ip.ip',0]) // when this value is between quotes, it gives more values
                ->andWhere(['is not','ip',null]) // added not to give IP values that are 0. So as not to break anything when consuming the endpoint
                ;

        // RUN QUERY AND GET CONTRACTS
        $contractsArr = $query->all(); //testing. 18408 reg

        // we need to substract all IPs from valid contracts from the previous main query.
        // this is because it can be the case that an old IP repeats itself on the Active Ips

        // Build a new query with the active contracts which also have forced or enabled status_account
        $validContractQuery = (new Query())
            // SELECT ATTRIBUTES
            ->select($selectArr)
            ->from('contract co')
            // DEFAULT JOINS
            ->leftJoin('customer cus', 'cus.customer_id = co.customer_id')
            ->leftJoin('connection as con', 'co.contract_id = con.contract_id')
            // WHERE CONDITIONS
            ->where(['and',
                ['in', 'con.status_account', ['forced','enabled']],
                ['in','co.status',['active']]
            ])
            ;

        // CONDITIONAL JOINS
        if($multiple) {
            $validContractQuery->leftJoin('company c', 'c.company_id = coalesce(cus.parent_company_id, cus.company_id)');
        } else {
            $validContractQuery->leftJoin('company c', 'c.company_id = cus.company_id');
        }

        // RUN QUERY AND GET CONTRACTS
        $validContracts = $validContractQuery->all();

        // make a search array for IPs
        $validIPs = array_column($validContracts, 'ip'); // *valid IPs are the same when flipped, cause the assignation algorithm cannot repeat them.
        $potentialIPs = array_column($contractsArr, 'ip');

        // Clean IPs in case some null value gets to here like it has happend before
        self::cleanIPArray($validIPs);
        self::cleanIPArray($potentialIPs);

        // Flipping 
        $at = array_flip($validIPs);
        $bt = array_flip($potentialIPs); 
        // checking
        $d = array_diff_key($bt, $at);
        $noConnectionIPs = array_keys($d);

        // foreach check
        foreach($contractsArr as $key => $contract) {
            // is the ip if this contract in the array that we are going to send to clip off connections? (then we invert the truth)
            if(!in_array($contract['ip'],$noConnectionIPs)){
                // var_dump("contract IP repeated on an active client: ",$contract);

                // unset the repeated contract/s (even though they are probably 1 or 2 cases..)
                unset($contractsArr[$key]); // bug?: for some reason unset() makes the array return as associative, which differs from V2
            }
        }
        $contracts = array_values($contractsArr); // this solves the ""BUG"" commented before
        return $contracts;
    }

    private static function cleanIPArray(&$ipArr){
        foreach($ipArr as $index => $ip){
            if(is_null($ip)) unset($ipArr[$index]);
        }
    }


    /**
     * Retorna un array con todos los contratos/conexiones del nodo pasado como parametro.
     *
     * @return array
     */
    public function actionFindByNode()
    {
        $post = Yii::$app->request->post();

        $code = (isset($post['code']) ?  $post['code'] : null);

        $response = [];
        if ($code) {
            $contracts = Contract::find()
                ->leftJoin('connection con', 'contract.contract_id = con.contract_id')
                ->leftJoin('node n', 'con.node_id = n.node_id')
                ->where('contract.status=\'active\' AND coalesce(n.subnet, contract.tentative_node)=:code ')
                ->addParams([':code'=>$code])
                ->all()
            ;

            foreach($contracts  as $contract) {
                $customer = $contract->customer;

                $contract = [
                    'customer_code' => $customer->code,
                    'contract_id' => $contract->contract_id,
                    'company'           => [
                        'company_id'    => ($customer->parentCompany ? $customer->parentCompany->company_id : $customer->company_id ),
                        'name'          => ($customer->parentCompany ? $customer->parentCompany->name : $customer->company->name ),
                    ]
                ];

                $response[] = $contract;
            }

        }

        return $response;
    }
    
    /**
     *  Setea el campo tentative_node en la tabla contract. 
     * 
     * @param integer $id Id de contrato
     * @param integer $node Subnet del nodo tentativo     * 
     * @return string
     */
    public function actionSetTentativeNode(){
        
        $post= Yii::$app->request->post();
        $node= (isset($post['node']) ?  $post['node'] : null);
        $id= (isset($post['id']) ?  $post['id'] : null);
       
        $contract= Contract::findOne(['contract_id'=> $post['id']]);
        if (!empty($contract)) {            
        
            
            if ($contract->setTentativeNode($post['node'])) {
                $response = [
                    'status' => 'success',
                    'messages' => 'Set tentative node successfull',
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => Yii::t('app', 'Can`t set tentative node'),
                ];
            }
        }else{
            $response=[
                'status' => 'error',
                'message'=> Yii::t('app', 'Parameters "id" and "node" are required'),
            ];
        }
        
        
        return $response;
        
    }

    /**
     * Devuelve los clientes que están incluidos en las notificaciones de explorador.
     * Este endpoint depende del siguiente comando ./yii browser-notification/save-customer-from-browser-notification-in-cache
     */
    public function actionGetBrowserNotificationCustomers()
    {
        $browser_notification_customers = \Yii::$app->cache->get('browser_notification_customers');

        return $browser_notification_customers ? $browser_notification_customers : [];
    }
}
