<?php

namespace app\modules\westnet\api\controllers;

use app\components\web\RestController;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\controllers\BillController;
use app\modules\sale\models\Customer;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\Contract;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class CustomerController extends RestController
{
    public $modelClass = 'app\modules\sale\models\Customer';

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
            'index' => ['GET', 'HEAD','POST'],
            'update-email-geocode' => ['PUT'],
        ];
    }


    /**
     * @SWG\Post(path="/isp/api/customer/index",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Retorna los datos del clientes segun el code o document_number y/o email pasado como parametro, en caso de enviar una peticion sin campos de filtro, retornara una lista de clientes.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="code", type="integer", description="Codigo del cliente (Opcional)"),
     *          @SWG\Property(property="document_number", type="string", description="Nro de Documento (Opcional)"),
     *          @SWG\Property(property="email", type="string", description="Email (Opcional)"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *            {
     *                   'customer_id': 234,
     *                   'name': 'RODAMIENTOS BRASIL SA',
     *                   'lastname': ',
     *                   'document_number': '30-25646545-7',
     *                   'document_type_id': 1,
     *                   'documentType': {
     *                       'document_type_id': 1,
     *                       'name': 'CUIT',
     *                       'code': 80,
     *                       'regex': '
     *                   },
     *                   'sex': null,
     *                   'email': ',
     *                   'phone': '4240800',
     *                   'phone2': ',
     *                   'phone3': ',
     *                   'phone4': ',
     *                   'fullAddress': ' (BRASIL 326 CDAD, )',
     *                   'geocode': '-32.8988839,-68.8194614',
     *                   'status': 'disabled',
     *                   'taxCondition': {
     *                       'tax_condition_id': 1,
     *                       'name': 'IVA Inscripto',
     *                       'exempt': 0
     *                   },
     *                   'company': {
     *                       'company_id': 8,
     *                       'name': 'Westnet'
     *                   },
     *                   'payment_code': '999923475'
     *             }
     *                      
     *         "
     *
     *     ),
     *
     * )
     *
     */

    /**
     * Lists all models.
     * Si no se especifica el id, se puede buscar por parametros con JSON
     * Los campos posibles son:
     * Ej.
     *  name
     *  document_number
     *  email
     *  code
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $post = Yii::$app->request->post();

        if(isset($post['code']) && $post['code']) {
            $customer = Customer::findOne(['code'=>$post['code']]);

            return $this->getCustomerArray($customer);
        }
        $searchHelper = new \app\components\helpers\SearchStringHelper();


        $searchModel = new \app\modules\sale\models\search\CustomerSearch();
        if(isset($post['name']) || isset($post['lastname'])) {
            $searchHelper->string = (isset( $post['name'] ) ? $post['name'] : '' ) . " " . (isset( $post['lastname'] ) ? " " . $post['lastname'] : '' );
            unset($post['name']);
            unset($post['lastname']);
        }
        $dataProvider = $searchModel->search(['CustomerSearch' => $post]);

        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');

        $where = "";
        foreach ($words as $word) {
            $where .= " (customer.name LIKE '".$word."' OR customer.lastname LIKE '".$word."') AND ";
        }

        $dataProvider
            ->query
            ->andWhere(substr ($where, 0, strlen($where)-4));
        //$dataProvider->pagination = false;

        $customers = [];
        $models = $dataProvider->getModels();
        foreach($models as $customer) {
            $customers[] = $this->getCustomerArray($customer);
        }

        return $customers;
    }


    /**
     * @SWG\Put(path="/isp/api/customer/update-email-geocode",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Actualiza el email y posicion geografica del domicilio de un cliente en particular.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="code", type="integer", description="Código del cliente"),
     *          @SWG\Property(property="geocode", type="string", description="Geocode (Opcional)"),
     *          @SWG\Property(property="email", type="string", description="Email (Opcional)"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               'status': true,
     *               'message': 'Actualizado',
     *               'geocode': 1,
     *               'email': 1
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'status': 'error',
     *               'message': 'No contract id specified.',
     *               'geocode' => 0,
     *                'email' => 0,
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /**
     * Actualiza el email y posicion geografica del domicilio de un cliente en particular
     *
     * @return array
     */
    public function actionUpdateEmailGeocode()
    {
        $post = Yii::$app->request->post();

        $response = [
            'status' => 'error',
            'message' => '',
            'geocode' => 0,
            'email' => 0,
        ];

        if(isset($post['id']) && $post['id']) {
            $email = (isset($post['email']) ? $post['email'] : '' ) ;
            $geocode = (isset($post['geocode']) ? $post['geocode'] : '' ) ;

            $customer = Customer::findOne(['code'=> $post['id']]);
            if(!$customer) {
                $response['message'] = Yii::t('app', 'The customer doesn\'t exists.');

            } else {
                if ($email) {
                    $customer->email = $email;
                    $response['email'] = $customer->updateAttributes(['email']);
                }

                if ($geocode) {
                    $address = $customer->address;
                    $address->geocode = $geocode;
                    $response['geocode'] = $address->updateAttributes(['geocode']);
                }
                $response['status'] = ($response['geocode'] || $response['email']);
            }
            $response['message'] = ( $response['status'] ? Yii::t('app', 'Updated') : Yii::t('app', 'No Updated') ) ;
        }

        return $response;
    }


    /**
     * @SWG\Post(path="/isp/api/customer/list-by-id",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Lista los clientes que son enviados por código del cliente seprados por coma. Ej: 87602,87603.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="code", type="string", description="Código del cliente"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               [
     *                   {
     *                       'customer_id': '87602',
     *                       'name': 'Seth Aaron',
     *                       'lastname': 'Flores ',
     *                       'document_number': '38758725',
     *                       'document_type_id': '2',
     *                       'documentType': {
     *                           'document_type_id': '2',
     *                           'name': 'DNI'
     *                       },
     *                       'sex': null,
     *                       'email': 'seth95444@gmail.com',
     *                       'phone': ',
     *                       'phone2': '2612533763',
     *                       'phone3': '2614716617',
     *                       'phone4': ',
     *                       'fullAddress': ', M-A C-28 I-Casa de un piso rejas y portón grises, ',
     *                       'geocode': '-32.964763783021056,-68.83001857468264',
     *                       'status': 'enabled',
     *                       'taxCondition': {
     *                           'tax_condition_id': '3',
     *                           'name': 'Consumidor Final'
     *                       },
     *                       'company': {
     *                           'company_id': '7',
     *                           'name': 'SERVICARGAS MENDOZA SA'
     *                       },
     *                       'payment_code': '21240008760278'
     *                   },
     *                   {
     *                       'customer_id': '87603',
     *                       'name': 'Laura Yésica',
     *                       'lastname': 'Farfán',
     *                       'document_number': '31834355',
     *                       'document_type_id': '2',
     *                       'documentType': {
     *                           'document_type_id': '2',
     *                           'name': 'DNI'
     *                       },
     *                       'sex': null,
     *                       'email': 'yesi_86_10@hotmail.com.ar',
     *                       'phone': ',
     *                       'phone2': '2612164042',
     *                       'phone3': '615659891',
     *                       'phone4': ',
     *                       'fullAddress': 'La casa esta pintada bordos  rejas blancas y en la parte del garage esta construido de 2 plantas , M-C C-11 I-casa bordo rejas blancas 2 plantas',
     *                       'geocode': '-32.96400256837732,-68.83027446168211',
     *                       'status': 'enabled',
     *                       'taxCondition': {
     *                           'tax_condition_id': '3',
     *                           'name': 'Consumidor Final'
     *                       },
     *                       'company': {
     *                           'company_id': '7',
     *                           'name': 'SERVICARGAS MENDOZA SA'
     *                       },
     *                       'payment_code': '21240008760300'
     *                   }
     *               ]
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'Error': true,
     *               'Message': 'No code specified.'
     *            }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /*
     * Lista los clientes que son enviados por parametro
     * @return array
     */
    public function actionListById()
    {
        // Este es un engañapichanga porque los cambios de multiempresa no se pasan todavia
        //  y necesito ponerlo en produccion.
        $multiple = (property_exists(Customer::className(), 'parent_company_id'));
        $post = Yii::$app->request->post();

        if(!isset($post['code'])){
            return [
                'Error' => true,
                'Message' => 'No code specified.'
            ];
        }

        /** @var Query $query */
        $query = new Query();
        $query
            ->select(['customer.*', 'add.*', 'dt.name as document_type_name', 'tc.name as tax_condition_name', 'z.name as zone_name', 'c.company_id', 'c.name as company_name'])
            ->from('customer')
            ->leftJoin('address add', 'customer.address_id = add.address_id')
            ->leftJoin('document_type dt', 'customer.document_type_id = dt.document_type_id')
            ->leftJoin('tax_condition tc', 'customer.tax_condition_id = tc.tax_condition_id')
            ->leftJoin('zone z', 'add.zone_id = z.zone_id')
            ->andWhere(['in', 'customer.code', explode(',', $post['code'])]);


        if($multiple) {
            $query->leftJoin('company c', 'c.company_id = coalesce(customer.parent_company_id, customer.company_id)');
        } else {
            $query->leftJoin('company c', 'c.company_id = customer.company_id');
        }

        $customers = [];
        $models = $query->all();
        foreach($models as $customer) {
            $customers[] = $this->getCustomerArray($customer);
        }

        return $customers;
    }



    /**
     * @SWG\Post(path="/isp/api/customer/account",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Lista los comprobates del cliente y su estado de cuenta.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="code", type="integer", description="Código del cliente"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               'account': [
     *                   {
     *                       'type': 'bill',
     *                       'number': '15449',
     *                       'date': '2019-10-21',
     *                       'total': '4130.01',
     *                       'status': 'closed',
     *                       'balance': '4130',
     *                       'pdf_key': 'MWxtWG5CYVZ5dTFBUXltQ2I1ZW10Zz09'
     *                   },
     *                   {
     *                       'type': 'payment',
     *                       'number': '14731',
     *                       'date': '2019-10-22',
     *                       'total': '4130',
     *                       'status': 'closed',
     *                       'balance': '0',
     *                       'pdf_key': ''
     *                   },
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'status': 'error',
     *               'message': 'No contract id specified.',
     *               'geocode' => 0,
     *                'email' => 0,
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /**
     * Lista los comprobates del cliente y su estado de cuenta.
     */
    public function actionAccount()
    {
        $post = Yii::$app->request->post();
        $code = (isset($post['code']) ?  $post['code'] : null);

        if(!isset($code)){
            return [
                'Error' => true,
                'Message' => 'No code specified.'
            ];
        }

        $response = [];
        if($code) {
            $customer = Customer::findOne(['code'=> $code]);

            // Traigo el estado de cuenta
            $searchModel = new PaymentSearch();
            $searchModel->customer_id = $customer->customer_id;
            $dataProvider = $searchModel->searchAccount($customer->customer_id, Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            $accounts = $dataProvider->getModels();

            $lastPaymentBalance = 0;
            foreach($accounts as $account) {
                // Pongo todos los movimientos
                $response['account'][] = [
                    'type'      => ($account['bill_id']=='0' ? 'payment' : 'bill' ),
                    'number'    => $account['number'],
                    'date'      => $account['date'],
                    'total'     => $account['total'],
                    'status'    => $account['status'],
                    'balance'   => $account['saldo'],
                    'pdf_key'   => ($account['bill_id']=='0' ? '' : $this->encrypt_decrypt($account['bill_id']) )
                ];
                $lastPaymentBalance += abs( ( $account['bill_id']=='0' && $account['status'] != 'cancelled' ? $account['total'] : 0  ) );
            }

            foreach($accounts as $account) {
                // Incluyo las facturas
                if ($account['bill_id'] != '0') {

                    $response['bill'][] = [
                        'bill_id' => $account['bill_id'],
                        'number' => $account['number'],
                        'date' => $account['date'],
                        'total' => $account['total'],
                        'status' => (($lastPaymentBalance - $account['total']) < 0 ? 'unpayed' : 'payed'),
                        'balance' => (($lastPaymentBalance - $account['total']) < 0 ? $lastPaymentBalance - $account['total'] : 0 ),
                        'pdf_key' => $this->encrypt_decrypt($account['bill_id'])
                    ];
                    $lastPaymentBalance -= $account['total'];
                }
            }
        }
        $cs = new CustomerSearch();
        $rs = $cs->searchDebtBills($customer->customer_id);
        $response['debt_bills'] = (!$rs ? 0 : $rs['debt_bills'] );

        //TODO: Por el momento hasta actualizar panel
        $response['bill'] = array_reverse($response['bill']);
        $response['account'] = array_reverse($response['account']);

        return $response;
    }


    /**
     * @SWG\Post(path="/isp/api/customer/find-by-contract",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Busca un customer basado en el numero de contrato.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="contract_id", type="integer", description="Número de Contrato"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               'customer_id': 3450,
     *               'name': 'ALSER LOGISTICO SRL  ###',
     *               'lastname': ',
     *               'document_number': '30-70848681-3',
     *               'document_type_id': 1,
     *               'documentType': {
     *                   'document_type_id': 1,
     *                   'name': 'CUIT',
     *                   'code': 80,
     *                   'regex': '
     *               },
     *               'sex': null,
     *               'email': 'olivencia@alserlogisticosrl.com.ar',
     *               'phone': '4811886',
     *               'phone2': '2615064019',
     *               'phone3': ',
     *               'phone4': ',
     *               'fullAddress': 'CARRIL GOMEZ 2053,',
     *               'geocode': '-32.8988839,-68.8194614',
     *               'status': 'disabled',
     *               'taxCondition': {
     *                   'tax_condition_id': 1,
     *                   'name': 'IVA Inscripto',
     *                   'exempt': 0
     *               },
     *
     *               'company': {
     *                   'company_id': 8,
     *                   'name': 'Westnet'
     *               },
     *               'payment_code': '07470000345078'
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'status': 'error',
     *               'message': 'No contract_id specified.',
     *               'geocode' => 0,
     *                'email' => 0,
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /**
     * Busca un customer basado en el numero de contrato.
     *
     * @return Customer|array
     */
    public function actionFindByContract()
    {
        $post = Yii::$app->request->post();
        $contract_id = (isset($post['contract_id']) ?  $post['contract_id'] : null);

        if(!isset($contract_id)){
            return [
                'Error' => true,
                'Message' => 'No contract_id specified.'
            ];
        }

        if($contract_id) {
            $contract = Contract::findOne(['contract_id'=>$contract_id]);
            if($contract) {
                return $this->getCustomerArray($contract->customer);
            }
        }

        return [];
    }


    /**
     * @SWG\Post(path="/isp/api/customer/bill-pdf",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Retorna un pdf con la factura.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="pdf_key", type="integer", description="Clave del PDF"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'status': 'error',
     *               'message': '',
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */


    /**
     * Retorna un pdf con la factura.
     *
     * @param $id
     * @return int|mixed|\yii\console\Response
     */
    public function actionBillPdf($pdf_key=null)
    {
        $post = Yii::$app->request->post();
        $id = ($pdf_key!=null ? $pdf_key : (isset($post['pdf_key']) ?  $post['pdf_key'] : null));
        $bill_id = $this->encrypt_decrypt($id, 'decrypt');

        $response = Yii::$app->runAction('/sale/bill/pdf', ['id'=> $bill_id]);

        return substr($response, strrpos($response, '%PDF-'));
    }


    /**
     * @SWG\Post(path="/isp/api/customer/find-by-category",
     *     tags={"Cliente"},
     *     summary="",
     *     description="Retorna los clientes por categoria",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="category", type="integer", description="Categoría"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'Error' => true,
     *               'Message' => 'No category specified.'
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /**
     * Busca Customers categoria
     *
     * @return array
     */
    public function actionFindByCategory()
    {
        $post = Yii::$app->request->post();
        $category = (isset($post['category']) ?  $post['category'] : null);

        if(!isset($category)){
            return [
                'Error' => true,
                'Message' => 'No category specified.'
            ];
        }

        if($category) {
            $subQuery = (new Query())
                ->select(['customer_id', new Expression('max(date_updated) maxdate') ])
                ->from('customer_category_has_customer')
                ->groupBy(['customer_id']);

            /** @var Query $query */
            $query = Customer::find();
            $models = $query
                ->innerJoin('customer_category_has_customer ccathc', 'ccathc.customer_id = customer.customer_id')
                ->innerJoin(['ccathc2'=> $subQuery], 'ccathc2.customer_id = customer.customer_id AND ccathc.date_updated = ccathc2.maxdate')
                ->where(['ccathc.customer_category_id'=>$category])
                ->all()
            ;

            foreach($models as $customer) {
                $customers[] = $this->getCustomerArray($customer);
            }
            return $customers;
        }

        return [];

    }

    /**
     * Encripta y desencripta el id.
     *
     * @param $id
     * @param string $action
     * @return bool|string
     */
    private function encrypt_decrypt($id, $action='encrypt')
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = '$_dan4VckasdfF=30923·';
        $secret_iv = '=·"%)"·$5 %GDFgsdfgsdf';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($id, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($id), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    private function getCustomerArray($customer)
    {   
        if(is_array($customer)) {
            return [
                'customer_id'       => $customer['code'],
                'name'              => $customer['name'],
                'lastname'          => $customer['lastname'],
                'document_number'   => $customer['document_number'],
                'document_type_id'  => $customer['document_type_id'],
                'documentType'      => [
                    'document_type_id' => $customer['document_type_id'],
                    'name' => $customer['document_type_name']
                ],
                'sex'               => $customer['sex'],
                'email'             => $customer['email'],
                'phone'             => $customer['phone'],
                'phone2'            => $customer['phone2'],
                'phone3'            => $customer['phone3'],
                'phone4'            => $customer['phone4'],
                'fullAddress'       => $this->getFullAddress($customer),
                'geocode'           => $customer['geocode'],
                'status'            => $customer['status'],
                'taxCondition'      => [
                    'tax_condition_id' => $customer['tax_condition_id'],
                    'name' => $customer['tax_condition_name'],
                ],
                'company'           => [
                    'company_id'    => $customer['company_id'],
                    'name'          => $customer['company_name'],
                ],
                'payment_code'      => $customer['payment_code']
            ];
        } else {
            return [
                'customer_id'       => $customer->code,
                'name'              => $customer->name,
                'lastname'          => $customer->lastname,
                'document_number'   => $customer->document_number,
                'document_type_id'  => $customer->document_type_id,
                'documentType'      => $customer->documentType,
                'sex'               => $customer->sex,
                'email'             => $customer->email,
                'phone'             => $customer->phone,
                'phone2'            => $customer->phone2,
                'phone3'            => $customer->phone3,
                'phone4'            => $customer->phone4,
                'fullAddress'       => ($customer->address ? $customer->address->getFullAddress() : '' ),
                'geocode'           => ($customer->address ? $customer->address->geocode : ''),
                'status'            => $customer->status,
                'taxCondition'      => $customer->taxCondition,
                'company'           => [
                    'company_id'    => ($customer->parentCompany ? $customer->parentCompany->company_id : $customer->company_id ),
                    'name'          => ($customer->parentCompany ? $customer->parentCompany->name : $customer->company->name ),
                ],
                'payment_code'      => $customer->payment_code
            ];
        }

    }

    private function getFullAddress($customer)
    {
        $fulladdress = '';
        $zone = $customer['zone_name'];

        if (!empty($customer['street'])) {
            $fulladdress = $customer['street'] . ' ' . $customer['number'];
        }
        if (!empty($customer['block'])) {
            $fulladdress = $fulladdress . ', M-' . $customer['block'];
        }
        if (!empty($customer['house'])) {
            $fulladdress = $fulladdress . ' C-' . $customer['house'];
        }
        if (!empty($customer['tower'])) {
            $fulladdress = $fulladdress . ' T-' . $customer['tower'];
        }
        if (!empty($customer['floor'])) {
            $fulladdress = $fulladdress . ' P-' . $customer['floor'];
        }
        if (!empty($customer['department'])) {
            $fulladdress = $fulladdress . ' D-' . $customer['department'];
        }
        if (!empty($customer['indications'])) {
            $fulladdress = $fulladdress . ' I-' . $customer['indications'];
        }
        return $fulladdress = $fulladdress . $zone;
    }

    public function actionCustomerExists($email, $code)
    {
        $customer = Customer::find()
                ->orWhere(['email'=>$email, 'code'=>$code])
                ->orWhere(['email2'=>$email, 'code'=>$code])
                ->one();

        return [
            'exists'    => ($customer ? true : false ),
            'customer'  => ($customer ? $this->getCustomerArray($customer) : null ),
        ];
    }

}
