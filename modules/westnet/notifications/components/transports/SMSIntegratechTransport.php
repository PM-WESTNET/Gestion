<?php

namespace app\modules\westnet\notifications\components\transports;

use app\modules\config\models\Config;
use app\modules\sale\models\Company;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\westnet\notifications\models\Campanas;
use app\modules\westnet\notifications\NotificationsModule;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use Yii;
use app\components\helpers\EmptyLogger;
use yii\base\Exception;
use yii\db\Query;
use app\modules\westnet\notifications\models\IntegratechMessage;
use app\modules\westnet\notifications\components\transports\IntegratechService;
use app\modules\westnet\notifications\models\Notification;

class SMSIntegratechTransport implements TransportInterface {

    public function features()
    {
        return [
            'manualSend' => true
        ];
    }

    public function send($notification, $force_send = false)
    {
        if(empty($notification->content)) {
            throw new Exception(NotificationsModule::t('app', 'The notification dont have content.'));
        }

        try {

            $msgs = $this->createIntegratechMessages($notification);
            $bd_status_notification = Notification::STATUS_ENABLED;

            $errors = [];
            $saved = 0;
            $sended = 0;
            $current_msg = 0;
            //Se verifica cada 100 mensajes que el estado de la notificación no ha cambiado. Esto es, para el manejo de
            //cancelacion de notificaciones en el momento en el que se esta ejecutando el cron.

            foreach ($msgs as $msg) {
                if($force_send != false){
                    $this->forceSendMessage($notification, $bd_status_notification, $current_msg, $msg, $saved, $sended, $errors);
                } else {
                    $this->sendMessage($notification, $bd_status_notification, $current_msg, $msg, $saved, $sended, $errors);
                }
                //Envio mensaje de prueba.
                if($notification->test_phone) {
                    //Verifico si tengo que hacer el envio de mensaje de test
                    if($sended != 0 && ($sended % $notification->test_phone_frecuency == 0)) {
                        $this->sendTestMessage($notification, $bd_status_notification, $current_msg, $saved, $sended, $errors);
                    }
                }
            }
            $this->updateNotificationLastSend($notification);
            if($errors){
                return [
                    'status' => 'error',
                    'error' => NotificationsModule::t('app', 'Cant send all messages.').NotificationsModule::t('app', 'Saved Messages:') .$saved.'. '. Yii::t('app', 'Sended Messages: ').$sended,
                ];
            }

            return [
                'status' => 'success',
                'message' => NotificationsModule::t('app', 'Saved Messages: ') .$saved .'. '. NotificationsModule::t('app', 'Sended Messages: ').$sended,
            ];

        }catch (\Exception $ex){
            error_log($ex->getMessage());

            \Yii::error($ex);
            throw $ex;
        }
    }
    private function updateNotificationLastSend($notification){
        $notification->updateAttributes(['last_sent' => date('Y-m-d')]);
    }
    private function sendMessage($notification, &$bd_status_notification, &$current_msg, $msg, &$saved, &$sended, &$errors){
        if($notification->isInRangeTimeLapse()){
            if($current_msg = 100){
                $db_notification = Notification::findOne($notification->notification_id);
                $bd_status_notification = $db_notification->status;
                $current_msg = 0;
            }

            if($bd_status_notification == Notification::STATUS_ENABLED){
                $response_send = $msg->send();
                if($response_send['saving']){
                    $saved ++;
                }
                if($response_send['response']){
                    $sended ++;
                }
                if($response_send['status'] == 'error'){
                    array_push($errors, Yii::t('app', 'Integratech message cant be sended'));
                }

            } else {
                $msg->markAsCancelled();
            }
            $current_msg ++;
        }

        return [
            'bd_status_notification' => $bd_status_notification,
            'current_msg' => $current_msg,
        ];

    }

    private function sendTestMessage($notification, &$bd_status_notification, &$current_msg, &$saved, &$sended, &$errors){

        $integratech_service = IntegratechService::getInstance();
        $message_content = "Se han enviado $sended mensajes";
        $msg = $integratech_service->addMessage($message_content, $notification->test_phone, IntegratechMessage::STATUS_PENDING, $notification->notification_id, 0);

        if($bd_status_notification == Notification::STATUS_ENABLED){
            $msg->send();
        }

        return [
            'bd_status_notification' => $bd_status_notification,
            'current_msg' => $current_msg,
        ];
    }

    private function forceSendMessage($notification, &$bd_status_notification, &$current_msg, $msg, &$saved, &$sended, &$errors){
        if($current_msg = 100){
            $db_notification = Notification::findOne($notification->notification_id);
            $bd_status_notification = $db_notification->status;
            $current_msg = 0;
        }
        if($bd_status_notification == Notification::STATUS_ENABLED){
            $response_send = $msg->send();
            if($response_send['saving']){
                $saved ++;
            }
            if($response_send['response']){
                $sended ++;
            }
            if($response_send['status'] == 'error'){
                array_push($errors, Yii::t('app', 'Integratech message cant be sended'));
            }
        }
        $current_msg ++;

        return [
            'bd_status_notification' => $bd_status_notification,
            'current_msg' => $current_msg,
        ];
    }

    private function createIntegratechMessages($notification){

        $integratech_service = IntegratechService::getInstance();
        $msgs = [];

        foreach($notification->destinataries as $destinataries){
            $query = $destinataries->getCustomersQuery(false);
            foreach($query->all() as $customer) {

                //Esto es para que se continuen enviando aquellos mensajes a los que se les interrumpió el envio por el cron
                if($customer['status_integratech'] == IntegratechMessage::STATUS_PENDING){
                    $msgs[] = IntegratechMessage::findOne($customer['integratech_message_id']);
                } else {
                    $phones = $this->getPhones($customer);

                    foreach($phones as $phone) {
                        if(trim($phone) != "") {
                            $msgs[] = $integratech_service->addMessage($this->replaceText($notification->content, $customer ), $phone, IntegratechMessage::STATUS_PENDING, $notification->notification_id, $customer['customer_id']);
                        }
                    }
                }
            }
        };

        return $msgs;
    }

    private function getPhones($customer){
        $phones = [];

        $p1 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone']));
        $p2 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone2']));
        $p3 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone3']));

        if(strlen($p1) > 7 ) {
            $phones[] = (string)$p1;
        }

        if(strlen($p2) > 7 && $p1 != $p2 ) {
            $phones[] = (string)$p2;
        }

        if(strlen($p3) > 7 && $p3 != $p1 && $p3 != $p2 ) {
            $phones[] = (string)$p3;
        }

        return $phones;
    }

    private function replaceText($text, $customer)
    {
        $replaced_text = $text;

        $replace_max_string = self::getMaxLengthReplacement();
        $replaced_text = str_replace('@Nombre', trim(substr($customer['name'], 0, $replace_max_string['@Nombre'])), $replaced_text);
        $replaced_text = str_replace('@Telefono1', substr($customer['phone'], 0, $replace_max_string['@Telefono1']), $replaced_text);
        $replaced_text = str_replace('@Telefono2', substr($customer['phone'], 0, $replace_max_string['@Telefono2']), $replaced_text);
        $replaced_text = str_replace('@Codigo', substr($customer['code'], 0, $replace_max_string['@Codigo']), $replaced_text);
        $replaced_text = str_replace('@CodigoDePago', substr($customer['payment_code'], 0, $replace_max_string['@CodigoDePago']), $replaced_text);
        $replaced_text = str_replace('@Nodo', substr($customer['node'], 0, $replace_max_string['@Nodo']), $replaced_text);
        $replaced_text = str_replace('@Saldo', substr($customer['saldo'], 0, $replace_max_string['@Saldo']), $replaced_text);
        $replaced_text = str_replace('@CodigoEmpresa', substr($customer['company_code'], 0, $replace_max_string['@CodigoEmpresa']), $replaced_text);
        $replaced_text = str_replace('@FacturasAdeudadas', substr($customer['debt_bills'], 0, $replace_max_string['@FacturasAdeudadas']), $replaced_text);
        $replaced_text = str_replace('@Estado', Yii::t('westnet', ucfirst($customer['status'])), $replaced_text);
        $replaced_text = str_replace('@Categoria', substr($customer['category'], 0, $replace_max_string['@Categoria']), $replaced_text);

        return $replaced_text;

    }

    /**
     * @param Notification $notification
     */
    public function export($notification){

        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());

        //Nombre de archivo
        try{
            $fileName = 'sms-contacts.xls';

            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');

            $excel = new PHPExcel();

            $excel->getProperties()
                ->setCreator("Arya By Quoma S.A.")
                ->setTitle("SMS Contacts");

            $excel->setActiveSheetIndex(0)
                ->setCellValue('A1', Yii::t('app', 'Name'))
                ->setCellValue('B1', Yii::t('app', 'Phone1'))
                ->setCellValue('C1', Yii::t('app', 'Phone2'))
                ->setCellValue('D1', Yii::t('app', 'Code'))
                ->setCellValue('E1', Yii::t('app', 'Payment Code'))
                ->setCellValue('F1', Yii::t('app', 'Node ID'))
                ->setCellValue('G1', Yii::t('app', 'Balance'))
                ->setCellValue('H1', Yii::t('app', 'Exp Datetime'))
                ->setCellValue('I1', Yii::t('app', 'Company'))
                ->setCellValue('J1', Yii::t('app', 'Debt Bills'))
                ->setCellValue('K1', Yii::t('app', 'Status'))
                ->setCellValue('L1', Yii::t('app', 'Category'))
                ->setCellValue('M1', Yii::t('app', 'Plan'))
                ->setCellValue('N1', Yii::t('app', 'Valor futuro del plan'));

            $i = 2;
            foreach($notification->destinataries as $destinataries){
                /** @var Query $query */
                $query = $destinataries->getCustomersQuery(false);
                foreach($query->all() as $customer) {
                    $phones = [];
                    $p1 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone']));
                    $p2 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone2']));
                    $p3 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone3']));
                    $p4 = trim(preg_replace('/[?&%$() \/-][A-Za-z]*/', '', $customer['phone4']));

                    if(strlen($p1) > 7 ) {
                        $phones[] = (string)$p1;
                    }

                    if(strlen($p2) > 7 && $p1 != $p2 ) {
                        $phones[] = (string)$p2;
                    }

                    if(strlen($p3) > 7 && $p3 != $p1 && $p3 != $p2 ) {
                        $phones[] = (string)$p3;
                    }

                    if(strlen($p4) > 7 && $p4 != $p1 && $p4 != $p2  && $p4 != $p3) {
                        $phones[] = (string)$p4;
                    }

                    foreach($phones as $phone) {
                        $plan = Plan::findOne($customer['plan']);
                        $future_price = $plan ? $plan->futureFinalPrice : '';
                        $company = Company::findOne($customer['customer_company']);
                        $excel->setActiveSheetIndex(0)
                            ->setCellValue('A' .$i, $customer['name'])
                            ->setCellValueExplicit('B' .$i, $phone, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('C' .$i, '')
                            ->setCellValueExplicit('D' .$i, $customer['code'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('E' .$i, $customer['payment_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('F' .$i, (isset($customer['node']) ? $customer['node'] : ''))
                            ->setCellValue('G' .$i, (isset($customer['saldo']) ? $customer['saldo'] : ''))
                            ->setCellValue('H' .$i, '')
                            ->setCellValueExplicit('I' .$i, $company ? $company->code : $customer['company_code'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('J' .$i, (isset($customer['debt_bills']) ? $customer['debt_bills'] : '' ))
                            ->setCellValue('K' .$i, Yii::t('westnet', ucfirst($customer['status'])))
                            ->setCellValue('L' .$i, $customer['category'])
                            ->setCellValue('M' .$i, $plan ? $plan->name : '')
                            ->setCellValue('N' .$i, $future_price ? Yii::$app->formatter->asCurrency($future_price) : '');
                        $i++;
                    }
                }
                $excel->getActiveSheet()->getStyle('A1:A'.$i)
                    ->getNumberFormat()
                    ->setFormatCode();
            }
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $objWriter->save('php://output');
        }catch (\Exception $ex){
            error_log($ex->getMessage());
        }

    }

    public static function abortMessages($notification_id){
        $pending_messages = IntegratechMessage::find()->where(['notification_id' => $notification_id])->all();
        foreach ($pending_messages as $message) {
            $message->updateAttributes(['status' => IntegratechMessage::STATUS_CANCELLED]);
        }
    }

    /**
     * @return array
     * devuelve el largo máximo que debe tener cada uno de los reemplazos
     */
    public static function getMaxLengthReplacement()
    {
        return [
            '@Nombre' => Config::getValue('notification-replace-@Nombre'),
            '@Telefono1' => Config::getValue('notification-replace-@Telefono1'),
            '@Telefono2' => Config::getValue('notification-replace-@Telefono2'),
            '@Codigo' => Config::getValue('notification-replace-@Codigo'),
            '@CodigoDePago' => Config::getValue('notification-replace-@CodigoDePago'),
            '@Nodo' => Config::getValue('notification-replace-@Nodo'),
            '@Saldo' => Config::getValue('notification-replace-@Saldo'),
            '@CodigoEmpresa' => Config::getValue('notification-replace-@CodigoEmpresa'),
            '@FacturasAdeudadas' => Config::getValue('notification-replace-@FacturasAdeudadas'),
            '@Categoria' => Config::getValue('notification-replace-@Categoria'),
        ];
    }
}