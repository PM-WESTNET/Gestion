<?php

namespace app\modules\westnet\notifications\components\transports;

use app\modules\config\models\Config;
use app\modules\mailing\components\sender\MailSender;
use app\modules\westnet\notifications\models\Notification;
use Yii;
use yii\base\Component;
use app\components\helpers\EmptyLogger;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
use yii\validators\EmailValidator;
use PHPExcel;
use PHPExcel_IOFactory;
/**
 * Description of EmailTransport
 *
 * @author mmoyano
 */
class EmailTransport implements TransportInterface {
    
    public function features()
    {
        return [
            'programmable'
        ];
    }

    public function export($notification)
    {
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());
        set_time_limit(0);

        //Nombre de archivo
        $fileName = 'mail-notification.xls';

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $excel = new PHPExcel();

        $excel->getProperties()
            ->setCreator("Arya By Quoma S.A.")
            ->setTitle("SMS Contacts");

        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', Yii::t('app', 'Name'))
            ->setCellValue('B1', Yii::t('app', 'Lastname'))
            ->setCellValue('C1', Yii::t('app', 'Email'));

        $i = 2;

        foreach ($notification->destinataries as $destinataries) {
            /** @var Query $query */
            $query = $destinataries->getCustomersQuery(false)->andWhere(['email_status' => 'active']);

            foreach ($query->batch(1000) as $customers) {
                foreach ($customers as $customer) {

                    $excel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $customer['name'])
                        ->setCellValue('B' . $i, $customer['lastname'])
                        ->setCellValue('C' . $i, $customer['email']);
                    $i++;
                }
                $excel->getActiveSheet()->getStyle('A1:A' . $i)
                    ->getNumberFormat()
                    ->setFormatCode();
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * @param Notification $notification
     * @return array
     */
    public function send($notification, $force_send = false){

        $emails = [];
        foreach($notification->destinataries as $destinataries){
            $emails = array_merge($emails, $destinataries->getEmails());
        }

        Yii::$app->cache->set('status_'.$notification->notification_id, 'in_proccess', 600);
        Yii::$app->cache->set('total_'.$notification->notification_id, count($emails), 600);

        $max_rate = (int)Config::getValue('aws_max_send_rate');

        //Le restamos 2 al maximo de cuota por segundo para asegurarnos nunca alcanzarla
        $chunks = array_chunk($emails, ($max_rate - 2), true);
        
        $ok = 0;
        $error = 'Error: ';

        try {
            // Obtengo la instancia para enviar emails.
            $transport = $notification->emailTransport;
            /** @var MailSender $mailSender */
            $mailSender = MailSender::getInstance(null, null, null, $notification->emailTransport);

            $layout = LayoutHelper::getLayoutAlias($notification->layout ? $notification->layout : 'Info');
            Yii::$app->mail->htmlLayout = $layout;
            $validator = new EmailValidator();
            //Por cada grupo
            foreach($chunks as $chunk){
                $messages = [];

                foreach($chunk as $toMail => $customer_data){
                    $toName = $customer_data['name'].' '.$customer_data['lastname'];
                    $clone = clone $notification;
                    $clone->content = self::replaceText($notification->content, $customer_data);
                    if ($validator->validate($toMail, $err)) {
                        $messages[] = $mailSender->prepareMessage(
                            ['email'=>$toMail, 'name' => $toName],
                            $notification->subject,
                            [ 'view'=> $layout ,'params' => ['notification' => $clone]]
                        );
                    }else{
                        $error .= " $toName <$toMail>; ";
                    }

                }

                $result = $mailSender->sendMultiple($messages);

                $ok += $result;
                Yii::$app->cache->set('success_'.$notification->notification_id, $ok, 600);
                Yii::$app->cache->set('error_message_'.$notification->notification_id, $error,600);
                Yii::$app->cache->set('total_'.$notification->notification_id, count($emails), 600);

                //Esperamos 3 segundos para enviar el siguiente paquete, esto evitara que se supere la cuota maxima por segundo
                sleep(3);
            }
        } catch(\Exception $ex) {
            Yii::$app->cache->delete('status_'.$notification->notification_id);
            Yii::$app->cache->delete('success_'.$notification->notification_id);
            Yii::$app->cache->delete('error_'.$notification->notification_id);
            Yii::$app->cache->set('error_message_'.$notification->notification_id, $ex->getTraceAsString(), 600);
            $error = $ex->getMessage();
            $ok = false;
        }


        Yii::$app->cache->delete('status_'.$notification->notification_id);
        if($ok){
            return [
                'status' => 'success',
                'count' => $ok
            ];
        }else{
            return [
                'status' => 'error',
                'error' => $error
            ];
        }
    }

    private function replaceText($text, $customer)
    {
        $replaced_text = $text;

        $replace_max_string = SMSIntegratechTransport::getMaxLengthReplacement();
        $replaced_text = str_replace('@Nombre', trim(substr($customer['name'], 0, $replace_max_string['@Nombre'])), $replaced_text);
        $replaced_text = str_replace('@Telefono1', substr($customer['phone'], 0, $replace_max_string['@Telefono1']), $replaced_text);
        $replaced_text = str_replace('@Telefono2', substr($customer['phone2'], 0, $replace_max_string['@Telefono2']), $replaced_text);
        $replaced_text = str_replace('@CodigoDeCliente', substr($customer['code'], 0, $replace_max_string['@Codigo']), $replaced_text);
        $replaced_text = str_replace('@PaymentCode', substr($customer['payment_code'], 0, $replace_max_string['@CodigoDePago']), $replaced_text);
        $replaced_text = str_replace('@Nodo', substr($customer['node'], 0, $replace_max_string['@Nodo']), $replaced_text);
        $replaced_text = str_replace('@Saldo', substr($customer['saldo'], 0, $replace_max_string['@Saldo']), $replaced_text);
        $replaced_text = str_replace('@CompanyCode', substr($customer['company_code'], 0, $replace_max_string['@CodigoEmpresa']), $replaced_text);
        $replaced_text = str_replace('@FacturasAdeudadas', substr($customer['debt_bills'], 0, $replace_max_string['@FacturasAdeudadas']), $replaced_text);
        $replaced_text = str_replace('@Estado', Yii::t('westnet', ucfirst($customer['status'])), $replaced_text);
        $replaced_text = str_replace('@Categoria', substr($customer['category'], 0, $replace_max_string['@Categoria']), $replaced_text);

        return $replaced_text;

    }
}