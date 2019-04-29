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
            'index' => ['GET', 'HEAD'],
            'update-email-geocode' => ['PUT'],
        ];
    }

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

        if(isset($post['id']) && $post['id']) {
            $customer = Customer::findOne(['code'=>$post['id']]);

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
        /** @var Query $query */
        $query = new Query();
        $query
            ->select(['customer.*', 'add.*', 'dt.name as document_type_name', 'tc.name as tax_condition_name', 'z.name as zone_name', 'c.company_id', 'c.name as company_name'])
            ->from('customer')
            ->leftJoin('address add', 'customer.address_id = add.address_id')
            ->leftJoin('document_type dt', 'customer.document_type_id = dt.document_type_id')
            ->leftJoin('tax_condition tc', 'customer.tax_condition_id = tc.tax_condition_id')
            ->leftJoin('zone z', 'add.zone_id = z.zone_id')
            ->andWhere(['in', 'customer.code', explode(',', $post['id'])]);


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
     * Lista los comprobates del cliente y su estado de cuenta.
     */
    public function actionAccount()
    {
        $post = Yii::$app->request->post();
        $id = (isset($post['id']) ?  $post['id'] : null);

        $response = [];
        if($id) {
            $customer = Customer::findOne(['code'=> $id]);

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

        return $response;
    }

    /**
     * Busca un customer basado en el numero de contrato.
     *
     * @return Customer|array
     */
    public function actionFindByContract()
    {
        $post = Yii::$app->request->post();
        $contract_id = (isset($post['contract_id']) ?  $post['contract_id'] : null);

        if($contract_id) {
            $contract = Contract::findOne(['contract_id'=>$contract_id]);
            if($contract) {
                return $this->getCustomerArray($contract->customer);
            }
        }

        return [];
    }

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
     * Busca Customers categoria
     *
     * @return array
     */
    public function actionFindByCategory()
    {
        $post = Yii::$app->request->post();
        $category = (isset($post['category']) ?  $post['category'] : null);

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
                'fullAddress'       => ($customer->address ? $customer->address->getFullAddress() : '' ),
                'geocode'           => $customer->address->geocode,
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
        $customer = Customer::findOne(['email'=>$email, 'code'=>$code]);

        return [
            'exists'    => ($customer ? true : false ),
            'customer'  => ($customer ? $this->getCustomerArray($customer) : null ),
        ];
    }

}
