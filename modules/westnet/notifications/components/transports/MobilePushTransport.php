<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 14/05/18
 * Time: 10:22
 */

namespace app\modules\westnet\notifications\components\transports;


use app\components\companies\User;
use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\sale\models\Product;
use app\modules\westnet\notifications\models\Notification;
use app\modules\westnet\notifications\models\Transport;

class MobilePushTransport extends Transport implements TransportInterface
{

    public function features()
    {
        return [
            'manualSent',
            'programmable'
        ];
    }

    public function send($notification, $force_send = false)
    {
        $mobile_push = new MobilePush();
        $mobile_push->title = $notification->name;
        $mobile_push->content = $notification->content;

        $id_product_payment_extension = Config::getValue('id-product_id-extension-de-pago');
        $product_payment_extension = Product::findOne($id_product_payment_extension);
        $product_payment_extension_value = 0;
        if($product_payment_extension){
            $product_payment_extension_value = $product_payment_extension->getFinalPrice();
        }

        if (!$mobile_push->save()){
            return ['status' => 'error', 'error' => 'Failed to save MobilePush'];
        }

        //Registramos todas las notificaciones que serán enviadas
        foreach ($notification->destinataries as $destinatary){
            $customers = $destinatary->getCustomersQuery()->all();
            if (count($customers) === 0) {
                return [ 'status' => 'error', 'error' => 'No customers to send'];
            }

            foreach ($customers as $customer){
                $customer['product_extension_value'] = $product_payment_extension_value;
                $mobile_push->addUserApp($customer['customer_id'], $customer);
            }
        }

        if($mobile_push->send()){
            $notification->updateAttributes(['status' => Notification::STATUS_SENT]);
        } else {
            $notification->updateAttributes(['status' => Notification::STATUS_ERROR]);
            return ['status' => 'error', 'error' => 'Failed to send '];
        }

        return ['status' => 'success'];
    }

    public function export($notification)
    {
        // TODO: Implement export() method.
    }
}