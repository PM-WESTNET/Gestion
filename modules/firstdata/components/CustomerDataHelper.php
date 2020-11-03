<?php

namespace app\modules\firstdata\components;

use app\modules\config\models\Config;

class CustomerDataHelper {


    /**
     * Devuevle el nro de tarjeta de credito del cliente recibido.
     * TODO: Se debe realizar la integracion con la api a desarrollar por WN. Por el momento se usa un mock
     */
    public static function getCustomerCreditCard($code)
    {
        $data = self::getCustomerData($code);

        if($data === false) {
            return false;
        }

        return $data->card_number;
    }

    public static function newCustomerData($code, $block1, $block2, $block3, $block4) 
    {
        $data = self::addCustomerData($code, $block1, $block2, $block3, $block4);

        if($data === false) {
            return false;
        }

        return true;
    }

    public static function modifyCustomerData($code, $block1, $block2, $block3, $block4, $status) 
    {
        $data = self::updateCustomerData($code, $block1, $block2, $block3, $block4, $status);

        if($data === false) {
            return false;
        }

        return true;
    }

    private static function getCustomerData($code)
    {
        $url = Config::getValue('firstdata_server_url'). ':'.Config::getValue('firstdata_server_port').'/get-data';
        $curl = curl_init($url);

        $data = json_encode(['customer_code' => $code]);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'X-api-token: '. Config::getValue('firstdata_api_token')
        ));

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            return false;
        }

        return $response;

    }

    private static function addCustomerData($code, $block1, $block2, $block3, $block4)
    {
        $url = Config::getValue('firstdata_server_url'). ':'.Config::getValue('firstdata_server_port').'/add-customer';
        $curl = curl_init($url);

        $data = json_encode([
            'customer_code' => $code,
            'block1' => $block1,
            'block2' => $block2,
            'block3' => $block3,
            'block4' => $block4,
            'status' => 'enabled'
        ]);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'X-api-token: '. Config::getValue('firstdata_api_token')
        ));

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            return false;
        }

        return $response;

    }

    private static function updateCustomerData($code, $block1, $block2, $block3, $block4, $status)
    {
        $url = Config::getValue('firstdata_server_url'). ':'.Config::getValue('firstdata_server_port').'/update-customer';
        $curl = curl_init($url);

        $data = json_encode([
            'customer_code' => $code,
            'block1' => $block1,
            'block2' => $block2,
            'block3' => $block3,
            'block4' => $block4,
            'status' => $status
        ]);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'X-api-token: '. Config::getValue('firstdata_api_token')
        ));

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            return false;
        }

        return $response;

    }
}

// Mock temporal hasta que se desarolle la api correspondiente
class CustomerDataMock {

    public function getData() {

        return [
            'credit_card' => "1234567891234567"
        ];
    }

}