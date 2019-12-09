<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 14/05/18
 * Time: 10:22
 */

namespace app\modules\westnet\notifications\components\transports;


use app\components\companies\User;
use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\UserApp;
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
        $mobile_push= new MobilePush();
        $mobile_push->title= $notification->name;
        $mobile_push->content = $notification->content;

        if (!$mobile_push->save()){
            return [
                'status' => 'error'
            ];
        }

        $destinataries= $notification->destinataries;

        foreach ($destinataries as $destinatary){
            $customers= $destinatary->getCustomersQuery()->all();
            if (count($customers) === 0) {
                return [
                    'status' => 'error'
                ];
            }

            foreach ($customers as $customer){
                $mobile_push->addUserApp($customer['customer_id']);
            }
        }

        $notification->updateAttributes(['status' => 'sent']);
        $mobile_push->send();

        return [
            'status' => 'success'
        ];
    }

    public function export($notification)
    {
        // TODO: Implement export() method.
    }
}