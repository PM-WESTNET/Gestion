<?php

namespace app\modules\westnet\isp\models;

use app\modules\sale\models\Customer;
use yii\base\Model;

/**
 * Description of Client
 *
 * @author smaldonado
 */
class Client extends Model {

    public $invoicing_bank_account;
    public $digest;
    public $taxpayer_identification_number;
    public $invoicing_bar_code_updated_at;
    public $invoicing_bar_code_file_name;
    public $einvoicing_bar_code_number;
    public $additional_information_file_updated_at;
    public $additional_information_file_file_name;
    public $id;
    public $external_client_number;
    public $email;
    public $details;
    public $created_at;
    public $phone_mobile;
    public $einvoicing_cobrodigital_electronic_code;
    public $einvoicing_cobrodigital_client_number;
    public $additional_information_file_file_size;
    public $national_identification_number;
    public $invoicing_bar_code_file_size;
    public $address;
    public $tmp_email;
    public $phone;
    public $name;
    public $invoicing_bar_code_content_type;
    public $invoicing_bank_name;
    public $additional_information_file_content_type;
    public $updated_at;
    public $city;


    /**
     * @inheritdoc
     */
    public function rules() {
        return [

            [
                [
                    'invoicing_bank_account', 'digest', 'taxpayer_identification_number', 'invoicing_bar_code_updated_at',
                    'invoicing_bar_code_file_name', 'einvoicing_bar_code_number', 'additional_information_file_updated_at',
                    'additional_information_file_file_name', 'id', 'external_client_number', 'email', 'details', 'created_at',
                    'phone_mobile', 'einvoicing_cobrodigital_electronic_code', 'einvoicing_cobrodigital_client_number',
                    'additional_information_file_file_size', 'national_identification_number', 'invoicing_bar_code_file_size',
                    'address', 'tmp_email', 'phone', 'name', 'invoicing_bar_code_content_type', 'invoicing_bank_name',
                    'additional_information_file_content_type', 'updated_at', 'city',
                ], 'safe'
            ],
            [['name'], 'required'],
            [['name'], 'string', 'min' => 3, 'max' => 255],
        ];
    }

    public function __construct($customer)
    {
        if ($customer instanceof Customer) {
            $this->external_client_number   = $customer->code;
            $this->name                     = ($customer->lastname ? $customer->lastname . " " : '' ) . ($customer->name ? $customer->name . " " : '' );
            $this->email                    = $customer->email;
            $this->phone                    = $customer->phone;
            $this->phone_mobile             = $customer->phone2;
            $this->address                  = ($customer->address ? $customer->address->getFullAddress(): '' );
            $this->city                     = ($customer->address->zone ? $customer->address->zone->system : '');
        } elseif(is_array($customer) ) {
            $this->load(['Client'=>$customer]);
            $this->name = ( array_key_exists('lastname', $customer)!==false ? $customer['lastname'] ." " : '' )  . ( array_key_exists('name', $customer)!==false ? $customer['name'] ." " : '' );
        }
    }
/*
    public function toArray()
    {
        return [
            'id' => $this->id,
            'external_client_number' => $this->external_client_number,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_mobile' => $this->phone_mobile,
            'address' => $this->address,
        ];
    }*/

    public function merge(Client $client)
    {
        $this->id                       = $client->id;
        $this->external_client_number   = $client->external_client_number;
        $this->name                     = $client->name;
        $this->email                    = $client->email;
        $this->phone                    = $client->phone;
        $this->phone_mobile             = $client->phone_mobile;
        $this->address                  = $client->address;
    }
}