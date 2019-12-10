<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 06/11/19
 * Time: 16:49
 */
namespace app\commands;

use app\modules\westnet\notifications\components\transports\CPortalTransport;
use app\modules\westnet\notifications\models\Notification;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use app\modules\westnet\notifications\models\Transport;
use yii\console\Controller;
use app\modules\checkout\models\search\PaymentSearch;
use Yii;

/**
 * Este comando consulta los clientes que tienen notificaciones de explorador y los guarda en cache, para ser consultados luego sin ejecutar la consulta de deudores.
 */
class BrowserNotificationController extends Controller
{

    /**
     * Guarda todos los clientes que se incluyan en una notificación por explorador a cache.
     */
    public function actionSaveCustomerFromBrowserNotificationInCache()
    {
        try {
            $transport = Transport::find()->where(['slug' => 'browser'])->one();
            $date = (new \DateTime('now'))->format('Y-m-d');
            $time = (new \DateTime('now'))->format('H:i:s');
            $date_of_the_week = strtolower(date("l", (new \DateTime('now'))->getTimestamp()));

            if($transport) {
                $notifications = Notification::find()
                    ->where(['transport_id' => $transport->transport_id, 'status' => Notification::STATUS_ENABLED])
                    ->andWhere(['<=', 'from_date', $date])
                    ->andwhere(['>=', 'to_date', $date])
                    ->andWhere(['<=', 'from_time', $time])
                    ->andwhere(['>=', 'to_time', $time])
                    ->andWhere(["$date_of_the_week" => 1])
                    ->all();

                $customers_array = [];

                $notification_qty = count($notifications);
                echo "Se encontraron $notification_qty notificaciones activas actualmente\n";



                foreach ($notifications as $notification) {
                    foreach ($notification->destinataries as $destinataries) {
                        $query = $destinataries->getCustomersQuery(false);

                        $browser_transport = new CPortalTransport();

                        foreach ($query->batch(1000) as $customers) {
                            foreach ($customers as $customer) {
                                array_push($customers_array, [
                                    'name' => trim($customer['name']),
                                    'lastname' => trim($customer['lastname']),
                                    'code' => $customer['code'],
                                    'ip' => long2ip($customer['ipv4']),
                                    'notification_content' => utf8_encode($browser_transport->replaceText($notification->content, $customer))
                                ]);
                            }
                        }
                    }
                }

                echo "Se agregaron ".count($customers_array) ." clientes\n";
                \Yii::$app->cache->set('browser_notification_customers', $customers_array, 2073600);
                echo "Proceso terminado \n";
            }
        } catch (\Exception $ex) {
            echo "error: " . $ex->getMessage();
            echo "\n";
            \Yii::info('Falla el proceso que guarda los clientes en cahe: ____'.$ex->getMessage() ."\n".$ex->getTraceAsString(), 'browser-notification-customers');
        }
    }
}