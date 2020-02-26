<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 9/10/19
 * Time: 16:57
 */

namespace app\modules\ticket\behaviors;

use app\components\helpers\DbHelper;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\config\models\Config;
use app\modules\ticket\models\Status;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class TicketBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

    }

    /**
     * Eventos que dispara el Behavior
     *
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
        ];
    }

    /**
     * Al cerrarse un pago, se deben cambiar los estados de los tickets de cobranza que el cliente del pago posea.
     */
    public function afterUpdate($event)
    {
        if($event->sender instanceof Payment) {
            if ($event->sender->status == Payment::PAYMENT_CLOSED ) {
                if($event->sender->customer_id) {
                    $customer = $event->sender->customer;

                    //Verifico que tenga tickets de cobranza
                    $dbname = DbHelper::getDbName(Status::getDb());
                    $tickets = $customer
                        ->getTickets()
                        ->leftJoin($dbname .'.status s', "s.status_id = $dbname.ticket.status_id")
                        ->where(["$dbname.ticket.category_id" => Config::getValue('cobranza_category_id'), "s.is_open" => 1])
                        ->all();

                    if($tickets) {

                        //Verifico que la deuda esté dentro del parámetro del tolerante.
                        $searchModel = new PaymentSearch();
                        $searchModel->customer_id = $customer->customer_id;
                        $total = $searchModel->accountTotal();

                        if(!($total <= -(Yii::$app->params['account_tolerance']))) {

                            //Actualizo los tickets en base a la cantidad de gestiones que tiene.
                            foreach ($tickets as $ticket) {
                                 $ticket_management_qty = count($ticket->ticketManagements);

                                if($ticket_management_qty == 0) {
                                    $ticket->status_id = Config::getValue('ticket_status_pago_sin_gestionar');
                                    $ticket->save();
                                }

                                if($ticket_management_qty > 0) {
                                    $ticket->status_id = Config::getValue('ticket_status_pago');
                                    $ticket->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}