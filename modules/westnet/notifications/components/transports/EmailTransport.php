<?php

namespace app\modules\westnet\notifications\components\transports;

use app\modules\mailing\components\sender\MailSender;
use app\modules\westnet\notifications\models\Notification;
use Yii;
use yii\base\Component;
use app\components\helpers\EmptyLogger;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
use yii\validators\EmailValidator;

/**
 * Description of EmailTransport
 *
 * @author mmoyano
 */
class EmailTransport implements TransportInterface {
    
    public function features()
    {
        return [
            'manualSent',
            'programmable'
        ];
    }
    
    public function export($notification){
        
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());
        
        //Nombre de archivo
        $fileName = 'emails.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        
        $output = fopen('php://output', 'w');
        
        //Encabezado:
        fputcsv($output, [ Yii::t('app', 'Name'), Yii::t('app', 'Lastname'), Yii::t('app', 'Email') ] );
        
        foreach($notification->destinataries as $destinataries){
            $query = $destinataries->getCustomersQuery();
            foreach($query->each() as $customer) {
                fputcsv($output, [ $customer['name'], $customer['lastname'], $customer['email'] ]);
            }
        }
        
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
        $chunks = array_chunk($emails, 50, true);
        
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
            }
        } catch(\Exception $ex) {
            $error = $ex->getMessage();
            $ok = false;
        }

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
        $replaced_text = str_replace('@Code', substr($customer['code'], 0, $replace_max_string['@Codigo']), $replaced_text);
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