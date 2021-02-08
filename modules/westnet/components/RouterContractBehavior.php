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
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\ticket\components\MesaTicket;
use app\modules\ticket\models\Assignation;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\mesa\components\request\RequiereRouterRequest;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

class RouterContractBehavior extends Behavior
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
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterInsert',
        ];
    }

    public function afterInsert($event)
    {
        try {
            $ticket = null;
            if($event->sender instanceof Contract) {
                /** @var Contract $contract  */
                $contract = $event->sender;

                $router_id = explode(',',Config::getValue('router_product_id'));
                // Determino si el contrato tiene router.
                $has_router = false;
                foreach ($contract->contractDetails as $contractDetail) {
                    error_log($contractDetail->product_id);
                    if( array_search($contractDetail->product_id, $router_id) !== false ){
                        $has_router = true;
                    }
                }

                $instalation_category_id = Config::getValue('instalation_category_id');
                // Como el id externo del ticket lo pongo en cada asignacion, las tengo que iterar.
                $request = new RequiereRouterRequest(Config::getValue('mesa_server_address'));
                $ticket = Ticket::findOne(['contract_id'=> $contract->contract_id, 'category_id'=>$instalation_category_id]);
                if($ticket) {
                    /** @var Assignation $assignation */
                    foreach( $ticket->assignations as $assignation) {
                        $request->requiere($assignation->external_id, $has_router);
                    }
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getFile() . " - " . $ex->getLine() . $ex->getMessage());
        }

    }
}