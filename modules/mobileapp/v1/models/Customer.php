<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 07/05/18
 * Time: 17:34
 */

namespace app\modules\mobileapp\v1\models;


use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\models\search\CustomerSearch;

class Customer extends \app\modules\sale\models\Customer
{
    public function fields()
    {
        return  [
            'name',
            'lastname',
            'documentType',
            'document_number',
            'taxCondition',
            'code',
            'email',
            'phone',
            'phone2',
            'phone3',
            'email2',
            'payment_code'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['status'], 'in', 'range' => ['enabled', 'disabled', 'blocked']],
            [['name', 'lastname'], 'string', 'max' => 150],
            [['document_number', 'email', 'email2'], 'string', 'max' => 45],
            [['document_type_id', 'address_id'], 'integer'],
            [['email', 'email2'], 'email'],
            [['company_id', 'customer_reference_id', 'code', 'publicity_shape', 'phone', 'phone2', 'phone3', 'screen_notification', 'sms_notification', 'email_notification', 'sms_fields_notifications', 'email_fields_notifications', '_notifications_way', '_sms_fields_notifications', '_email_fields_notifications'], 'safe'],
            [['code', 'payment_code'], 'unique'],
        ];


        return $rules;
    }



        public function getAccount(){

        $searchModel = new PaymentSearch();
        $searchModel->customer_id = $this->customer_id;
        $dataProvider = $searchModel->searchAccount($this->customer_id, []);
        $dataProvider->pagination = false;
        $accounts = $dataProvider->getModels();
        $balance = 0;
        $response = [
            'account' => [],
            'bill' => [],
        ];

        $lastPaymentBalance = 0;
        foreach( array_reverse($accounts) as $key => $account) {

            $lastPaymentBalance += abs( ( $account['bill_id']=='0' && $account['status'] != 'cancelled' ? $account['total'] : 0  ) );
            $balance += $account['bill_id']=='0' && $account['status'] != 'cancelled' ? $account['total'] : $account['total'] * -1;
            if ($key >= (count($accounts) - 10)){

                // Pongo los 10 ultimos movimientos
                $response['account'][] = [
                    'type'      => ($account['bill_id']=='0' ? 'payment' : 'bill'),
                    'type_label'  => \Yii::t('app', $account['type']),
                    'number'    => $account['number'],
                    'date' => \Yii::$app->formatter->asDate($account['date'], 'dd-MM-yyyy'),
                    'timestamp' => strtotime($account['date']),
                    'total'     => $account['total'],
                    'status'    => \Yii::t('app', ucfirst($account['status'])),
                    'balance'   => $account['saldo'],
                ];

                // Incluyo las facturas
                if ($account['bill_id'] != '0') {

                    $response['bill'][] = [
                        'bill_id' => $account['bill_id'],
                        'type'  => \Yii::t('app', $account['type']) ,
                        'number' => $account['number'],
                        'date' => \Yii::$app->formatter->asDate($account['date'], 'dd-MM-yyyy'),
                        'timestamp' => strtotime($account['date']),
                        'total' => abs($account['total']),
                        'status' => (($lastPaymentBalance - $account['total']) < 0 ? \Yii::t('app', ucfirst('unpayed')) : \Yii::t('app', ucfirst('payed'))),
                        'balance' => (($lastPaymentBalance - $account['total']) < 0 ? $lastPaymentBalance - $account['total'] : 0 ),
                        'pdf_key' => $this->encrypt_decrypt($account['bill_id'])
                    ];
                    $lastPaymentBalance -= $account['total'];
                }
            }
        }

        /**
         * Ordeno los movimientos y las facturas por fechas en forma descendente
         */
        $bills= $response['bill'];
        $bubble= null;
        for ($i=0; $i < count($bills); $i++){
            for ($j=0; $j < (count($bills)-1); $j++){
                if (strtotime($bills[$j]['date']) < strtotime($bills[$j+1]['date'])){
                    $bubble= $bills[$j];
                    $bills[$j]= $bills[$j+1];
                    $bills[$j+1]= $bubble;
                }
            }
        }
        $response['bill']= $bills;

        $movements= $response['account'];
        $bubble= null;
        for ($i=0; $i < count($movements); $i++){
            for ($j=0; $j < (count($movements)-1); $j++){
                if (strtotime($movements[$j]['date']) < strtotime($movements[$j+1]['date'])){
                    $bubble= $movements[$j];
                    $movements[$j]= $movements[$j+1];
                    $movements[$j+1]= $bubble;
                }
            }
        }

        $response['account']= $movements;


        $cs = new CustomerSearch();
        $rs = $cs->searchDebtBills($this->customer_id);
        $response['debt_bills'] = (!$rs ? 0 : $rs['debt_bills'] );
        $response['balance'] = $balance;

        return $response;

    }


    public function getEcopagos(){
        $ecopagos= [];
        foreach ($this->contracts as  $contract){
            foreach ($contract->connection->node->ecopagos as $ecopago){
                $ecopagos[$contract->contract_id][$ecopago->ecopago_id]= $ecopago->description;
            }
        }

        return $ecopagos;
    }

    public function getDestinataries(){
        $destinataries= [];

        if (!empty($this->phone)){
            $destinataries[]= ['label' => 'phone', 'value' => $this->phone];
        }

        if (!empty($this->phone2)){
            $destinataries[]= ['label' => 'phone2', 'value' => $this->phone2];
        }

        if (!empty($this->phone3)){
            $destinataries[]= ['label' => 'phone3', 'value' => $this->phone3];
        }

        if (!empty($this->email)){
            $destinataries[]= ['label' => 'email', 'value' => $this->email];
        }

        if (!empty($this->email2)){
            $destinataries[]= ['label' => 'email2', 'value' => $this->email2];
        }

        return $destinataries;
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

    public static function clearDocumentNumber($document){

        $clean_document = $document;
        $clean_document = str_replace('-', '', $clean_document);
        $clean_document = str_replace('/', '', $clean_document);
        $clean_document = trim($clean_document);
        error_log($clean_document);

        return $clean_document;
    }
}