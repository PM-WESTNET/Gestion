<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 24/05/19
 * Time: 13:07
 */

namespace app\modules\westnet\notifications\components\transports;


use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\InfobipMessage;
use app\modules\westnet\notifications\models\InfobipResponse;
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
                $dest[] = '549'.$phone;
            }
        }else {
            $dest = '549'.$to;
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
            $data = json_decode($response);

            if ($data->messages) {
                foreach ($data->messages as $sms) {
                    $msj = new InfobipMessage([
                        'bulkId' => $data->bulkId,
                        'messageId' => $sms->messageId,
                        'to' => $sms->to,
                        'status' => $sms->status->name,
                        'status_description' => $sms->status->description,
                        'sent_timestamp' => time()
                    ]);

                    $msj->save();
                }
            }


            return true;
        }
    }

    public static function getResponses()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://8l5pe.api.infobip.com/sms/1/inbox/reports",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic ". base64_encode(Config::getValue('infobip_user').':'.Config::getValue('infobip_pass')),
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            error_log($err);
            return [
                'status' => 'error',
                'error' => $err
            ];
        } else {
            $data = json_decode($response);

            echo $response;

            if ($data->results) {
                foreach ($data->results as $message) {
                    $response = new InfobipResponse([
                        'from' => $message->from,
                        'to' => $message->to,
                        'content' => $message->text,
                        'keyword' => $message->keyword,
                        'received_timestamp' => $message->receivedAt
                    ]);

                    $response->save();
                }
            }

            return [
                'status' => 'success',
                'count' => $data->messageCount
            ];
        }
    }

}