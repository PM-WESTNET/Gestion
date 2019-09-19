<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 22/03/19
 * Time: 14:52
 */

namespace app\modules\ticket\behaviors;

use app\modules\ticket\components\factories\GeneratedActionsFactory;
use app\modules\ticket\models\Ticket;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class GenerateActionBehavior extends Behavior
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
        /**TODO: Creo que seria mejor que esto se disparase en el AFTER_UPDATE, porque corre el riesgo de generarse la accion y que
         * el ticket no se actualice por algun problema
         */
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    /**
     * @param $event
     * Genero una accion, si el estado esta configurado para ello
     */
    public function afterUpdate($event)
    {
        //Es un objet de la clase Ticket?
        if($event->sender instanceof Ticket) {
            //El estado ha cambiado?
            if(isset($event->changedAttributes['status_id'])) {
                //El estado genera una acción?
                if($event->sender->status->generate_action) {
                    GeneratedActionsFactory::generate($event->sender);
                }
            }
        }
    }
}