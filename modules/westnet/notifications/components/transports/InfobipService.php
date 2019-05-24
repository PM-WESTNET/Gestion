<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 24/05/19
 * Time: 13:07
 */

namespace app\modules\westnet\notifications\components\transports;


use app\modules\config\models\Config;
use yii\base\InvalidConfigException;

class InfobipService
{


    /**
     * @param string $from Telefono o nombre del remitente
     * @param array|string $to String con el telefono de destino o array de telefonos
     * @param $message Mensaje a enviar
     * @return bool
     */
    public static function sendSimpleSMS($from, $to, $message)
    {
        $dest= [];

        if(is_array($to)) {
            foreach ($to as $phone) {
                $dest[] = '54'.$phone;
            }
        }else {
            $dest = '54'.$to;
        }

        $data = [
            'from' => $from,
            'to' => $dest,
            'text' => $message
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => trim(Config::getValue('infobip_base_url'), '/').'/text/single',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic ". base64_encode(Config::getValue('infobip_user').':'.Config::getValue('infobip_pass')),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Yii::info("cURL Error #:" . $err);
            return false;
        } else {
            \Yii::info($response);
            return true;
        }
    }

    public function sendMultipleSMS($messages)
    {

        $data= [
            'bulkId' => uniqid().rand(0,10),
            'messages' => $messages,
            "tracking" => [
                "track" => "SMS",
                "type" => "MY_CAMPAIGN"
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => trim(Config::getValue('infobip_base_url'), '/')."/text/advanced",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic ". base64_encode(Config::getValue('infobip_user').':'.Config::getValue('infobip_pass')),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Yii::info("cURL Error #:" . $err);
            return false;
        } else {
            \Yii::info($response);
            return true;
        }
    }


}