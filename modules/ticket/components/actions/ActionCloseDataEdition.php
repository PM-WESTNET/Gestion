<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 07/06/19
 * Time: 18:46
 */

namespace app\modules\ticket\components\actions;


use app\modules\mobileapp\v1\models\AppFailedRegister;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;

class ActionCloseDataEdition implements ActionInterface
{

    public static function generate(Ticket $ticket)
    {
        $appFiledRegister = AppFailedRegister::findOne(['ticket_id' => $ticket->ticket_id]);

        if ($appFiledRegister) {
            $appFiledRegister->updateAttributes(['status' =>  'closed']);
        }
    }
}