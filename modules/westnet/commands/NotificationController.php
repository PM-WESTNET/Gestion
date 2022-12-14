<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\westnet\commands;

use app\modules\westnet\notifications\components\transports\InfobipService;
use yii\console\Controller;
use app\modules\westnet\notifications\components\scheduler\Scheduler;
use app\modules\westnet\notifications\models\Notification;
use yii\helpers\Console;
use app\modules\alertsbot\controllers\TelegramController;

/**
 * Description of NotificationController
 *
 * @author martin
 */
class NotificationController extends Controller
{
    /**
     * Action para envio de notificaciones. Debe ser cargado en un cron daily.
     */
    public function actionNotify()
    {
        try{
                
            $this->stdout("Start\n");
            $schedulers = Scheduler::getSchedulerObjects();
            
            foreach($schedulers as $scheduler){
                $this->scheduler($scheduler);
            }
            
            //Parte fundamental del codigo:
            $this->stderr("\n\n");
            $this->stderr("****** ** ** *****    ***** **   ** **** \n", Console::FG_YELLOW);
            $this->stderr("  **   ** ** **       **    ***  ** **  **\n", Console::FG_YELLOW);
            $this->stderr("  **   ***** *****    ***** ** * ** **  **\n", Console::FG_YELLOW);
            $this->stderr("  **   ** ** **       **    **  *** **  **\n", Console::FG_YELLOW);
            $this->stderr("  **   ** ** *****    ***** **   ** **** \n", Console::FG_YELLOW);

        }
        catch(\Exception $ex){
            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: westnet/notification/notify ****', $ex);
        }
    }
    
    /**
     * Envia las notificaciones que aplican al scheduler recibido como parametro
     * @param type $scheduler
     */
    private function scheduler($scheduler)
    {
        $this->stdout("\n********************\n".$scheduler->name().":\n", Console::BOLD);
        
        $query = Notification::find();
        $query->where(['status' => Notification::STATUS_ENABLED]);
        $query->andWhere(['not',['scheduler' => NULL]]);
        $query->andWhere(['OR', ['<','last_sent', date('Y-m-d')], ['last_sent' => null]]);

        $scheduler->mergeQuery($query);
        $notifications = $query->all();

        var_dump($query->createCommand()->getRawSql());

        foreach($notifications as $notification){
            $this->stdout('Notification: ');
            $this->stdout($notification->name."\n", Console::BOLD);
            
            $transport = $notification->transport;
            
            $this->stderr('Transport: ');
            $this->stderr($transport->name."\n", Console::BOLD);
            
            $this->stderr("Sending...\n\n");

            //Si es desde email o mobilepush, lo corremos con el mutex correspondiente
            if($transport->name == 'Email') {
                if (\Yii::$app->mutex->acquire('send_emails_'.$notification->notification_id)){
                    $notification->updateAttributes(['status' => 'in_process']);
                    $status = $transport->send($notification);
                    $notification->updateAttributes(['status' => 'enabled']);
                    \Yii::$app->mutex->release('send_emails_'. $notification->notification_id);
                }
            } elseif ($transport->name == 'Mobile Push'){
                if (\Yii::$app->mutex->acquire('send_mobile_push_'.$notification->notification_id)){
                    $notification->updateAttributes(['status' => 'in_process']);
                    $status = $transport->send($notification);
                    $notification->updateAttributes(['status' => 'enabled']);
                    \Yii::$app->mutex->release('send_mobile_push_'. $notification->notification_id);
                }
            } else {
                $status = $this->sendNotification($notification);
            }

            if($status['status'] == 'success'){
                $this->stdout("\nSUCCESS!\n\n\n", Console::BOLD);
            }else{
                if(array_key_exists('error',$status)){
                    $this->stdout("\nDETAILS:".$status['error']."\n\n\n");
                }
                $this->stdout("\nERROR: ".$status['error']."\n\n\n");
            }
        }
        
    }

    public function actionReceiveInfobipResponses() {

        echo "Consultando.....";
        echo "\n";

        $response= InfobipService::getResponses();

        echo 'Resultado: '. $response['status'];
        echo "\n";

        echo 'Respuestas: '. $response['count'];
        echo "\n";
    }

    /**
     * Comando para enviar las campa??as de email no calendarizadas. Implementa mutex
     */
    public function actionSendEmails() {
        try{

            $notifications = Notification::find()
                ->innerJoin('transport t', 't.transport_id=notification.transport_id')
                ->andWhere(['t.name' => 'Email', 'notification.status' => Notification::STATUS_PENDING])
                ->andWhere(['or',['notification.scheduler' => null], ['notification.scheduler' => '']])
                ->all();
        
            foreach ($notifications as $notification) {
                if (\Yii::$app->mutex->acquire('send_emails_'.$notification->notification_id)){
                    $this->stdout("\n" . 'Start send for notification_id: ' . $notification->notification_id . ' at ' . date("Y-m-d h:i:s") . "\n");

                    $notification->updateAttributes(['status' => Notification::STATUS_IN_PROCESS]);
                    $transport = $notification->transport;
                    $result = $transport->send($notification);
                    $this->stdout('Notification batch end for: ' . $notification->notification_id . ' at ' . date("Y-m-d h:i:s") . "\n");

                    if ($result['status'] === 'success') {
                        $notification->updateAttributes(['status' => Notification::STATUS_SENT]);

                    }else if($result['status'] === 'paused') {
                        $notification->updateAttributes(['status' => Notification::STATUS_PAUSED]);

                    }else {
                        $notification->updateAttributes(['status' => Notification::STATUS_ERROR]);
                    }
                    $this->stdout('Notification status change: ' . $notification->status . ' at ' . date("Y-m-d h:i:s") . "\n");
                    
                    \Yii::$app->mutex->release('send_emails_'. $notification->notification_id);
                    $this->stdout('mutex released at ' . date("Y-m-d h:i:s") . "\n");
                }

            }
        }
        catch(\Exception $ex){
            $this->stdout('Error Catch at' . date("Y-m-d h:i:s") . "\n" . "With message " . $ex->getMessage() . "\n");
            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: westnet/notification/send-emails ****', $ex);
        }
    }

    /**
     * Comando para enviar las notificaciones push no calendarizadas. Implementa mutex
     */
    public function actionSendMobilePush()
    {
        try{
                
            $notifications = Notification::find()
                ->innerJoin('transport t', 't.transport_id=notification.transport_id')
                ->andWhere(['t.name' => 'Mobile Push', 'notification.status' => 'pending'])
                ->andWhere(['or',['notification.scheduler' => null], ['notification.scheduler' => '']])
                ->all();

            foreach ($notifications as $notification) {
                if (\Yii::$app->mutex->acquire('send_mobile_push_'.$notification->notification_id)){
                    $notification->updateAttributes(['status' => 'in_process']);
                    $transport = $notification->transport;
                    $transport->send($notification);

                    $notification->updateAttributes(['status' => 'sent']);
                    \Yii::$app->mutex->release('send_mobile_push_'. $notification->notification_id);
                }
            }
        }
        catch(\Exception $ex){
            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: westnet/notification/send-mobile-push ****', $ex);
        }
    }
}
