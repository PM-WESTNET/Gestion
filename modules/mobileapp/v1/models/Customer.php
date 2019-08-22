<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 07/05/18
 * Time: 17:34
 */

namespace app\modules\mobileapp\v1\models;


use app\modules\checkout\models\Payment;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\config\models\Config;
use app\modules\sale\models\Bill;
use app\modules\sale\models\BillType;
use app\modules\sale\models\search\CustomerSearch;

class Customer extends \app\modules\sale\models\Customer
{
    public function fields()
    {
        return  [
            'name',
            'lastname',
            'fullName',
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

    public function getBillsToShow()
    {
        $excluded_bill_type = BillType::findOne(['name' => 'Cupón de Pago']);
        $all_bills = $this->getBills()->where(['status' => Bill::STATUS_CLOSED])->andWhere(['not',['bill_type_id' => $excluded_bill_type->bill_type_id]])->orderBy('timestamp', SORT_DESC)->limit(10)->all();
        $bills = [];

        foreach ($all_bills as $bill) {
            $bills[] = [
                'bill_id' => $bill->bill_id,
                'date' => $bill->date,
                'number' => str_pad($bill->company->defaultPointOfSale->number, 4, "0", STR_PAD_LEFT).'-'. str_pad($bill->number, 8, "0", STR_PAD_LEFT) ,
                'pdf_key' => $this->encrypt_decrypt($bill->bill_id),
                'total' => $bill->total,
            ];
        }

        return $bills;
    }

    public function getPaymentsToShow()
    {
        $all_payments = $this->getPayments()->where(['status' => Payment::PAYMENT_CLOSED])->orderBy(['timestamp'=> SORT_DESC])->limit(5)->all();
        $payments = [];

        foreach ($all_payments as $payment) {
            $payments[] = [
                'date' => $payment->date,
                'number' => str_pad($payment->number, 8, "0", STR_PAD_LEFT),
                'pdf_key' => $this->encrypt_decrypt($payment->payment_id),
                'total' => $payment->amount,
            ];
        }

        return $payments;
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

        if (!empty($this->phone4)){
            $destinataries[]= ['label' => 'phone4', 'value' => $this->phone4];
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

    public function getShowBills() {
        if ($this->company_id != Config::getValue('ecopagos_company_id')) {
            return true;
        }

        return false;
    }
}