<?php

namespace app\modules\alertsbot\controllers;

use yii\web\Controller;
use Yii;

use function PHPSTORM_META\type;

/**
 * Default controller for the `alertsbot` module
 */
class TelegramController extends Controller
{
    /**
     * Gets some variables and makes the message structure to then send it to a specific chat_id
     */
    public static function sendMessage($message, $telegram_chat_id = null){
        if(!isset(Yii::$app->params['telegram'])) return false; // if params var isnt set ret false
        $telegram_bot = Yii::$app->params['telegram']['bots']['isp_gestion_alerts'];
        $token = $telegram_bot['http-api-token'];
        if(is_null($telegram_chat_id)) $telegram_chat_id = $telegram_bot['telegram-chat-id'];

        // prepare and send
        return self::send($message, $telegram_chat_id, $token);
    }

    /**
     * sends message to a specific chat_id
     */
    private function send($message_text, $telegram_chat_id, $token){
        $data = [
            'text' => $message_text,
            'chat_id' => $telegram_chat_id
        ];
        $get_query = http_build_query($data);
        return file_get_contents("https://api.telegram.org/bot".$token."/sendMessage?" . $get_query );
    }


    /**
     * sends load average of system to telegram
     * load average can be used to see system task stress at any time as an average streched across 1,5 and 15 minutes.
     */
    public function actionMessageSysLoadAvg($la){

        $company = NULL;

        if(isset(Yii::$app->params['gestion_owner_company'])){
            $company = Yii::$app->params['gestion_owner_company'];
            strtolower($company);
        };

        if($company == 'westnet'){
            if ($la > 5.6) {
                return TelegramController::sendMessage('ALERTA! REVISAR!, el Load Average del sistema es de ' . $la . "\n" .'Alerta WESTNET');
            };
        }else if($company == 'bigway'){
            if ($la > 2.8) {
                return TelegramController::sendMessage('ALERTA! REVISAR!, el Load Average del sistema es de ' . $la . "\n" .'Alerta BIGWAY');
            };
        }
    }

    /**
     * gets a formatted output from a php exception
     * this is used to concat when sending messages to telegram
     * @param Exception
     * @return String
     */
    public static function getFormattedMessageFromException($ex){
        $formatted_text = "\n";
        $formatted_text .= 'At - '.(new \DateTime())->format('d-m-Y H:i:s').' (server time) '."\n";
        $formatted_text .= 'File - '.$ex->getFile(). "\n";
        $formatted_text .= 'Error - '.$ex->getMessage()."\n";
        $formatted_text .= 'Line - '.$ex->getLine()."\n";
        $formatted_text .= 'Trace - '.$ex->getTraceAsString()."\n";
        return $formatted_text;
    }

    /**
     * This function works with the sendMessage() but concats a process name for specific 
     * error messages with exceptions and traces.
     * 
     * @param strings
     * @return mixed
     */
    public static function sendProcessCrashMessage($process_name, $ex, $telegram_chat_id = null){
        $msg = $process_name."\n".self::getFormattedMessageFromException($ex);
        return self::sendMessage($msg, $telegram_chat_id);
    }

}
