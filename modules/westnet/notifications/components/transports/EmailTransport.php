<?php

namespace app\modules\westnet\notifications\components\transports;

use app\modules\config\models\Config;
use app\modules\mailing\components\sender\MailSender;
use app\modules\sale\models\Customer;
use app\modules\westnet\notifications\models\Notification;
use app\modules\westnet\notifications\models\NotificationHasCustomer;
use Yii;
use yii\base\Component;
use app\components\helpers\EmptyLogger;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
use yii\validators\EmailValidator;
use PHPExcel;
use PHPExcel_IOFactory;
use app\components\helpers\FileLog;
use \yii\helpers\VarDumper;
use yii\helpers\Url;
use app\modules\checkout\models\Payment;
//use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\Bill;
use app\modules\sale\controllers\BillController;
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
            ->setCellValue('C1', Yii::t('app', 'Email'))
            ->setCellValue('D1', Yii::t('app', 'Email 2'));

        $i = 2;

        foreach ($notification->destinataries as $destinataries) {
            /** @var Query $query */
            $query = $destinataries->getCustomersQuery(false)->andWhere(['email_status' => 'active']);

            foreach ($query->batch(1000) as $customers) {
                foreach ($customers as $customer) {
                    $email2_active = $customer['email2_status'] == Customer::EMAIL_STATUS_ACTIVE ? true : false;

                    $excel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $customer['name'])
                        ->setCellValue('B' . $i, $customer['lastname'])
                        ->setCellValue('C' . $i, $customer['email'])
                        ->setCellValue('D' . $i, $email2_active ? $customer['email2'] : '');
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
        //log_email('Comenzando envio de notificaci??n: ' . $notification->notification_id . '  ' . date('Y-m-d H:i'));
        if(Yii::$app->request->isConsoleRequest) echo 'Comenzando envio de notificaci??n: ' . $notification->notification_id . '  ' . date("Y-m-d h:i:s") . "\n";
        
        $customers = NotificationHasCustomer::GetCustomerToCampaign($notification->notification_id);
        echo(count($customers)." - customers PENDING TO SEND found \n");
        $already_sent = NotificationHasCustomer::GetCustomerToCampaign($notification->notification_id, Notification::STATUS_SENT);
        echo(count($already_sent)." - customers ALREADY SENT found \n");
        Yii::$app->cache->set('status_'.$notification->notification_id, 'in_proccess', 600);
        Yii::$app->cache->set('total_'.$notification->notification_id, count($customers), 600);

        $max_rate = (int)Config::getValue('aws_max_send_rate');
        $time_sleep = 2 / $max_rate;

        $ok = 0;
        $error = 'Error: ';

        try {
            $layout = LayoutHelper::getLayoutAlias($notification->layout ? $notification->layout : 'Info');
            Yii::$app->mail->htmlLayout = $layout;
            $validator = new EmailValidator();

            if(!empty($customers)){
                foreach($customers as $customer){
                    $status_notification = Notification::findOne(['notification_id' => $notification->notification_id])->status;
                    if($status_notification === "pending" || $status_notification === "in_process"){

                        $customerObject = Customer::findOne(['customer_id' => $customer['customer_id']]);
                        if(!empty($customerObject) && empty($customerObject->hash_customer_id)){
                            $customerObject->hash_customer_id = md5($customerObject->customer_id);
                            $customerObject->save(false);
                        }

                        $customer['hash_customer_id'] = $customerObject->hash_customer_id;
                        $result = 0;
                        /** @var MailSender $mailSender */
                        $mailSender = MailSender::getInstance(null, null, null, $notification->emailTransport);

                        //log_email($customer);

                        $toName = $customer['name'].' '.$customer['lastname'];
                        $toMail = $customer['email'];
                        if(Yii::$app->request->isConsoleRequest) echo date("Y-m-d h:i:s") .' cliente: ' . $toName . ' correo: ' .  $toMail ."\n";

                        //clone content and replace all "@" commands
                        $clone = clone $notification;
                        $clone->content = self::replaceText($notification->content, $customer);
                        
                        if ($validator->validate($toMail, $err)) {
                            $result = (strpos($notification->content, '@PdfAdjuntoFactura'))
                                    ? $this->AttachmentPdf($customer['customer_id'],$customer['email'])
                                    : $mailSender->prepareMessageAndSend(
                                [
                                    'email'=>$customer['email'], 
                                    'name' => $toName
                                ],
                                $notification->subject,
                                [ 
                                    'view'=> $layout ,
                                    'params' => [
                                        'notification' => $clone
                                        ]
                                ],
                                [],
                                [],
                                []
                            );
                            // echo "\nresultvalue:\n";
                            // var_export($result);
                            // echo "\n";
                            // result = true or 1
                            if(($result) and !is_string($result)){
                                NotificationHasCustomer::MarkSendEmail($customer['email'], $notification->notification_id, 'sent');
                            }
                            // result boolean FALSE or STRING
                            else{
                                // separate string - "false/0" value posibility
                                $obs = null;
                                if(is_string($result)){
                                    $obs = VarDumper::dumpAsString($result);
                                }
                                NotificationHasCustomer::MarkSendEmail($customer['email'], $notification->notification_id, 'error', $obs);

                                // increment error counter
                                $ok ++;
                            }

                        }else{                            
                            if(!isset($toName)){
                                if(Yii::$app->request->isConsoleRequest) echo 'toName not setted ' . "\n";
                            }
                            if(!isset($toMail)){
                                if(Yii::$app->request->isConsoleRequest) echo 'toMail not setted ' . "\n";
                            }
                            $error .= " $toName <$toMail>; ";
                            
                            $errStr = 'Invalid email: '.$toMail.' - Customer: '.$customer['code'].' - Date: '.date("Y-m-d h:i:s");
                            // log_email($errStr);
                            if(Yii::$app->request->isConsoleRequest) echo $errStr."\n";
                            NotificationHasCustomer::MarkSendEmail($customer['email'], $notification->notification_id, 'error', $errStr);
                        }
                        
                        Yii::$app->cache->set('success_'.$notification->notification_id, $ok, 600);
                        Yii::$app->cache->set('error_message_'.$notification->notification_id, $error,600);
                        Yii::$app->cache->set('total_'.$notification->notification_id, count($customers), 600);

                        sleep($time_sleep);
                    }else{
                        Yii::info('Envio de camapa??a pausada', 'emails');
                        return [
                            'status' => 'paused',
                            'count' => $ok
                        ];
                    }
                }
                //log_email('Fin de envio de notificaci??n: ' . $notification->notification_id . '  ' . date('Y-m-d H:i'));
            }else{
                $error = 'No existen destinatarios PENDIENTES para la notificacion seleccionada.';
            }
        } catch(\Exception $ex) {
            Yii::$app->cache->delete('status_'.$notification->notification_id);
            Yii::$app->cache->delete('success_'.$notification->notification_id);
            Yii::$app->cache->delete('error_'.$notification->notification_id);
            Yii::$app->cache->set('error_message_'.$notification->notification_id, $ex->getTraceAsString(), 600);

            $error = "ERROR ".$ex->getMessage()." - FILE ".$ex->getFile()."(".$ex->getLine().")";
            $notification->updateAttributes(['error_msg' => $error]);
            $ok = false;
            if(Yii::$app instanceof Yii\console\Application) echo 'Error: ' . $error . '  ' . date("Y-m-d h:i:s") . "\n";
        }


        Yii::$app->cache->delete('status_'.$notification->notification_id);
        if($ok){
            Yii::info('Total de correos enviados: ' . $ok, 'emails');
            Yii::info('Finalizado Correctamente', 'emails');
            return [
                'status' => 'success',
                'count' => $ok
            ];
        }else{
            Yii::info('Finalizado con error', 'emails');
            return [
                'status' => 'error',
                'error' => $error
            ];
        }
    }

    private function replaceText($text, $customer)
    {
        $replaced_text = $text;
        $url_redirect_gestion = Config::getConfig('siro_url_redirect_gestion')->item->description;
        $url_redirect_gestion = str_replace('${customer_id}',$customer['hash_customer_id'],$url_redirect_gestion);
        
        $replace_max_string = SMSIntegratechTransport::getMaxLengthReplacement();
        $replaced_text = str_replace('@Nombre', trim($customer['name']), $replaced_text);
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
        $replaced_text = str_replace('@BotonDePago', "  <a href='".$url_redirect_gestion."'". "style='background-color: #1c3ae2; font-size: 20px; font-weight: bold; text-decoration: none; padding: 12px 18px;margin: 20px 0; color: #ffffff; border-radius: 10px; display: inline-block; mso-padding-alt: 0;'>Bot??n de Pago</a>", $replaced_text);
        $replaced_text = str_replace('@LogoSiro', "</br><img src=".Url::base().'/images/logo-siro.png'." alt='LogoSiro' style='border:0;width:80px;'>", $replaced_text);
        $replaced_text = str_replace('@PdfAdjuntoFactura', "", $replaced_text);

        return $replaced_text;
    }

    /**
     * Env??a la ultima factura cerrada por email al cliente.
     */
    //Url::toRoute(['/sale/bill/email', 'id' => $model['bill_id'], 'from' => 'account_current', 'email' => $email])
    // changed original start from PAYMENT ID to a CUSTOMER ID. original function can be found in : modules/checkout/controllers/PaymentController.php
    public  function createLatestBillPDF($customer_id){ 
        //$modelBillSearch = BillSearch::searchLastBillByCustomerId($customer_id);
        $bill_id_by_customer = Bill::find()
            ->select(['b.bill_id', 'b.class'])
            ->from(['bill b'])
            ->leftJoin('customer c', 'c.customer_id = b.customer_id')
            ->where(['c.customer_id' => $customer_id])
            ->andWhere(['b.status' => 'closed'])
            ->orderBy(['b.date'=>SORT_DESC])
            ->one();
        
        // some bill found for this user
        if(!empty($bill_id_by_customer)){ 
            $model = Bill::findOne($bill_id_by_customer->bill_id);

            $pdf = $model->makePdf($model->bill_id);
    
            $pdf = substr($pdf, strrpos($pdf, '%PDF-'));
            $fileName = "/tmp/" . 'Comprobante' . sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number) . "-" . $model->customer_id . ".pdf";
            $file = fopen($fileName, "w+");
            fwrite($file, $pdf);
            fclose($file);

            return [$fileName];
        }

        // no single bill found
        return [];
        
    }

    /**
     * This function sends the lastest closed bill for the specified customer_id.
     * This function was made by a previous programmer so i dont really know why
     * it was made using CURLs. 
     * this will crash in the case of a firewall or connection drop. i suggest it
     * being redone
     * 
     * @return mixed
     */
    public function AttachmentPdf($customer_id, $email){
        // default error value. later changes if email is sent ok
        $response = false;

        //todo: fix potential error that the lastest bill can be a credit note and not an invoice
        // check that bill_type relation has class 'app\modules\sale\models\bills\Bill' . because that is the class used by invoice instances (fact A , B , C. M)
        // find the lastest closed bill from customer.
        $bill = Bill::find()
                ->select(['b.bill_id', 'b.class'])
                ->from(['bill b'])
                ->leftJoin('customer c', 'c.customer_id = b.customer_id')
                ->where(['c.customer_id' => $customer_id])
                ->andWhere(['b.status' => 'closed'])
                ->orderBy(['b.date'=>SORT_DESC])
                ->one();

        // if a bill is found related to customer
        if(!empty($bill)){ 

            //*DOC https://www.yiiframework.com/doc/guide/2.0/en/runtime-routing
            // remember this routing is done by an Application Console so routing is a tiny bit different
            $url = Url::toRoute(
                [
                    '/sale/bill/email-console',
                    'id' => $bill->bill_id,
                    'from' => 'account_current',
                    'email' => $email,
                ]);
            // echo $url;
            // $url ="https://gestion.bigway.com.ar/index.php?r=/sale/bill/email-console&id=".$bill."&from=account_current&email=".$email;

            // init curl
            $ch = curl_init();
            // set curl options array
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
            );
            // sets options
            curl_setopt_array($ch,$options);

            // curl exec. At this point the /sale/bill/email-console triggers and sends an email in the instance context of yii2 CONSOLE App
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code

            // for some reason , curl response is a stringyfied version of the boolean it represents..
            if( ($response == '1') or ($response == '0') ){
                $response = intval($response);
            }
            
            if ( $httpCode != 200 ){
                echo "Return code is {$httpCode} \n".curl_error($ch);
            } else {
                echo htmlspecialchars($response);
            }
            // close connection
            curl_close($ch);

        }
        else{
            $response = Yii::t('app', 'This customer has no closed bill that could be sent');
        }
        return $response;
    }
}
