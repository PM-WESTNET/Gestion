<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/01/16
 * Time: 14:55
 */

namespace app\modules\westnet\components;


use app\modules\config\models\Config;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\components\MesaTicket;
use app\modules\ticket\models\Assignation;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\mesa\components\request\RequiereRouterRequest;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

class MesaTicketContractBehavior extends Behavior
{
    /**
     * @inheritdoc
     */

    public $events = [ ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert' ];

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
        return $this->events;
    }

    public function afterInsert($event)
    {
        $this->createMesaTicket($event->sender);
    }

    public function createMesaTicket($obj)
    {
        $ticket = null;
        if($obj instanceof Contract) {

            $contract = $obj;

            /**
             * Si tiene un plan que sea de fibra, la categoria del ticket a crear no es la misma que la de las instalaciones comunes
             */
            if($contract->hasFibraPlan()){
                $instalation_category_id = Config::getValue('fibra_instalation_category_id');
            } else {
                $instalation_category_id = Config::getValue('instalation_category_id');
            }

            if (!$instalation_category_id) {
                throw new Exception(Yii::t('app', 'Parameter not found: {parameter}', ['parameter' => 'instalation_category_id']));
            }

            //$category = Category::findOne(['category_id'=>$instalation_category_id]);
            $ticket = new Ticket();
            $ticket->contract_id = $contract->contract_id;
            $ticket->customer_id = $contract->customer_id;
            $ticket->category_id = $instalation_category_id;
            $ticket->title = Yii::t('app', 'Instalation Ticket');
            $ticket->content = Yii::t('app', 'Instalation Ticket');
            $ticket->status_id = Config::getValue('ticket_new_status_id');
            $ticket->user_id = Yii::$app->user->id;
            $ticket->external_tag_id = Ticket::getTagByName('LLEVAR ADS ORIGINAL');
            $ticket->setUsers([Yii::$app->user->id]);
            $ticket->save(false);
        }
    }
}