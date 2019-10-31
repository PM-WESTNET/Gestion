<?php

namespace app\modules\westnet\notifications\components\transports;

use app\modules\westnet\notifications\models\Campanas;
use app\modules\westnet\notifications\models\IntegratechMessage;
use app\modules\westnet\notifications\NotificationsModule;
use yii\base\Exception;
use app\modules\config\models\Config;

/**
 * Class IntegratechService
 * Clase es la encargada de comunicarse con integratech.
 */
class IntegratechService
{
    private static $instance = null;

    public static function getInstance()
    {
        if(self::$instance===null) {
            self::$instance = new IntegratechService();
        }

        return self::$instance;
    }

    public function addMessage($message, $phone, $status = IntegratechMessage::STATUS_PENDING, $notification_id, $customer_id)
    {
        $msg = new IntegratechMessage();
        $msg->load([
            'IntegratechMessage'=>[
                'message' => $message,
                'phone' => $phone,
                'status' => $status,
                'notification_id' => $notification_id,
                'customer_id' => $customer_id
            ]
        ]);

        if($msg->save()) {
            return $msg;
        } else {
            throw new Exception(NotificationsModule::t('app', 'The message cant be saved.')." - " . print_r($msg->getErrors(),1));
        }
    }

    public static function sendSMS($phone, $message){
        $url = Config::getValue('integratech_url');
        $status = 'error';
        $sms_data= [
                'USERNAME' => Config::getValue('integratech_username'),
                'PASSWORD' => Config::getValue('integratech_password'),
                'DESTADDR' => '54'.$phone, //El destinatario tiene que ir en formato internacional, con codigo de pais incluido
                'MESSAGE' => $message
            ];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == 200){
                $status = 'success';
            }

            return [
                    'status' => $status,
                    'response' => json_decode($response, true)
            ];
    }

    public static function updateIntegratechMessageStatus($message_ids, $status)
    {
        return IntegratechMessage::updateAll(['status'=>$status], ['in', 'integratech_message_id', $message_ids] );
    }
}