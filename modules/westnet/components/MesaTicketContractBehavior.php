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
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
        ];
    }

    public function afterInsert($event)
    {
        $ticket = null;
        if($event->sender instanceof Contract) {
            /** @var Contract $contract */
            $contract = $event->sender;
            $instalation_category_id = Config::getValue('instalation_category_id');
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
            $ticket->save();

        }
    }
}