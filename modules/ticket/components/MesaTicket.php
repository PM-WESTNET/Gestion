<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 21/06/16
 * Time: 14:43
 */

namespace app\modules\ticket\components;
use app\modules\config\models\Config;
use app\modules\ticket\models\Assignation;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\mesa\components\request\TicketRequest;
use yii\base\Exception;


/**
 * Class MesaTicket
 * Clase para el manejo de ticket de Mesa
 *
 * @package app\modules\ticket\components
 */
class MesaTicket
{
    /**
     * Retorna el request de Ticket
     * @return TicketRequest
     */
    private function getRequest()
    {
        return new TicketRequest(Config::getValue('mesa_server_address'));
    }

    /**
     * @param Ticket $ticket
     * @return null
     * @throws \Exception
     * @internal param $autor_id
     * @internal param $asignado_id
     * @internal param $categoria_id
     * @internal param $descripcion
     * @internal param $contrato_id
     */
    public static function createTicket(Ticket $ticket)
    {
        if (Config::getValue('app_testing')) {
            return true;
        }

        $api = self::getRequest();

        try{
            $notify = false;
            $notify_this = self::isParentNotified($ticket->category);
            // Si notifica, lo va a hacer solo a un usuario
            // Pero como se le asigna al usuario local, se guarda en esa asignacion el id externo.
            // Creo todos los tickets para los usuarios asignados
            /** @var Assignation $assign */
            foreach( $ticket->assignations as $assign) {
                if($notify_this && !$notify) {
                        $external_id = $api->create(
                            $ticket->contract->vendor->external_user_id, 
                            $ticket->category->external_user_id,
                            $ticket->category_id, 
                            $ticket->title, 
                            $ticket->contract_id, 
                            $ticket->contract->customer->code, 
                            $ticket->external_tag_id
                        );
                   
                        $notify = true;
                    if(!$external_id) {
                        throw new \Exception($api->error);
                    }
                    if($external_id) {
                        $assign->external_id = $external_id;
                        $assign->save(false);
                    }
                }
            }
            return true;
        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param Ticket $ticket
     * @return bool
     * @throws \Exception
     */
    public static function updateTicket(Ticket $ticket)
    {
        $api = self::getRequest();

        try{
            // Creo todos los tickets para los usuarios asignados
            /** @var Assignation $assign */
            foreach( $ticket->assignations as $assign) {
                $api->update($assign->external_id, $ticket->status->name, $ticket->customer_id, $ticket->user_id);
            }
            return true;
        } catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param Ticket $ticket
     * @return array
     */
    public static function getExternalTicket(Ticket $ticket)
    {
        $tickets = [];
        $api = self::getRequest();

        /** @var Assignation $assign */
        foreach( $ticket->assignations as $assign) {
            $tickets[] = $api->findById($assign->external_id);
        }

        return $tickets;
    }

    public static function isParentNotified(Category $category)
    {
        return (!$category ? false : (
            $category->parent ? self::isParentNotified($category->parent) : $category->notify
        ) );
    }
}