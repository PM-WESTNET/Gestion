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
                $contracts = Contract::find()
                    ->leftJoin('customer', 'contract.customer_id = customer.customer_id')
                    ->andWhere(['customer.code'=>$customer_id])
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
            /* ->andWhere([ // an error was encountered that was relationed with the portal cautivo that warned customers. some customers where missing due to this extra filters
                'cus.status' => Customer::STATUS_ENABLED,
                'con.status' => Connection::STATUS_ENABLED
            ]) */
        ;

        if($multiple) {
            $query->leftJoin('company c', 'c.company_id = coalesce(cus.parent_company_id, cus.company_id)');
        } else {
            $query->leftJoin('company c', 'c.company_id = cus.company_id');
        }

        //var_dump(count($query->all()));die();
        $contracts = $query->all();

        $searchModel = new PaymentSearch();
        foreach($contracts as $contract) {
            $searchModel->customer_id = $contract['customer_id'];
            unset($contract['customer_id']);
            $response[] = array_merge($contract, [
                'due' => $searchModel->accountTotal()
            ]);
        }

        return $response;
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
