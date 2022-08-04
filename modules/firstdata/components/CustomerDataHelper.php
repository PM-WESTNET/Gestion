<?php

namespace app\modules\firstdata\components;

use Yii;
use app\modules\config\models\Config;

class CustomerDataHelper {


    /**
     * Devuevle el nro de tarjeta de credito del cliente recibido.
     * @return Null if card wasnt found
     * @return False if api didnt work
     * @return Object if some data was retrieved
     */
    public static function getCustomerCreditCard($code, $hide_card = false)
    {
        $data = self::getCustomerData($code);
        if ($data['status'] === 'not found') {
            return null;
        }
        // if data == false, the api failed.
        if($data === false) {
            return false;
        }
        if( $hide_card ) return $data['last_four'];
        return $data['card_number'];
    }

    /**
     * Devuevle el nro de tarjeta de credito del cliente recibido.
     */
    // public static function getCustomerHiddenCreditCard($code)
    // {
    //     $data = self::getCustomerData($code);

    //     if($data === false) {
    //         return false;
    //     }

    //     Yii::trace($data);

    //     return $data['last_four'];
    // }

    /**
     * Crea el registros de datos del Cliente en el server de firstdata
     */
    public static function newCustomerData($code, $block1, $block2, $block3, $block4) 
    {
        $data = self::addCustomerData($code, $block1, $block2, $block3, $block4);

        if($data === false) {
            return false;
        }

        return true;
    }

    /**
     * Modifica los datos del cliente
     */
    public static function modifyCustomerData($code, $block1, $block2, $block3, $block4, $status) 
    {
        $data = self::updateCustomerData($code, $block1, $block2, $block3, $block4, $status);

        if($data === false) {
            return false;
        }

        return true;
    }

    /**
     * Realiza la llamada a la api de firstdata para buscar los datos de un cliente
     * @return False if api didnt work
     * @return Object if data was retrieved
     */
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

        $response_data = json_decode($response, true);

        Yii::trace($response_data);
        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            return false;
        }

        return $response_data;

    }


    /**
     * Llama a la api de firstdata para insertar un nuevo registro de datos
     */
    private static function addCustomerData($code, $block1, $block2, $block3, $block4)
    {
        $url = Config::getValue('firstdata_server_url'). ':'.Config::getValue('firstdata_server_port').'/add-customer';
        $curl = curl_init($url);

        $data = json_encode([
            'code' => $code,
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

        Yii::trace($response);

        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            return false;
        }

        return $response;

    }

    /**
     * Llama a la api de firstdata para actualizar un registro de datos
     */
    private static function updateCustomerData($code, $block1, $block2, $block3, $block4, $status)
    {
        $url = Config::getValue('firstdata_server_url'). ':'.Config::getValue('firstdata_server_port').'/update-customer';
        $curl = curl_init($url);

        $data = json_encode([
            'code' => $code,
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

        Yii::trace($response);

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