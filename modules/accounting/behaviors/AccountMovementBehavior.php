<?php

namespace app\modules\accounting\behaviors;

use app\components\db\ActiveRecord;
use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\BaseMovement;
use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\models\AccountConfig;
use app\modules\accounting\models\AccountMovementRelation;
use ReflectionClass;
use yii\base\Behavior;


/**
 * Class AccountMovementBehavior
 *
 * Busca las configuraciones de cuentas contables que tiene la clase que dispara el Behavior
 * para despues instanciar la clase que implemente MovementInterface y ejecutar los movimientos
 * contables.
 *
 * @package app\modules\accounting\behaviors
 */
class AccountMovementBehavior extends Behavior
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
            ActiveRecord::EVENT_AFTER_INSERT    => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE    => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE    => 'afterDelete',
        ];
    }

    /**
     * Función que se ejecuta cuando se hace el insert de la clase que contiene el Behavior
     *
     * @param $event
     */
    public function afterInsert($event)
    {
        $this->move("insert", $event);
    }

    /**
     * Función que se ejecuta cuando se hace el update de la clase que contiene el Behavior
     *
     * @param $event
     */
    public function afterUpdate($event)
    {
        $this->move("update", $event);
    }

    /**
     * Función que se ejecuta cuando se hace un delete de la clase que contiene el Behavior
     *
     * @param $event
     */
    public function afterDelete($event)
    {
        $this->move("delete", $event);
    }

    /**
     * Ejecuta los movimientos para todas  las clases asociadas a la clase del modelo que envia el behavior.
     *
     * @param $action string insert o update
     * @param $event Evento
     * @param $debit  Clase Account usada para el debito
     * @param $credit Clase Account usada para el credito
     */
    private function move($action, $event)
    {
        // Obtengo la clase que llama el behavior
        $senderClass = get_class($event->sender);

        $classes = [] ;
        $classes[] = $senderClass;

        $class = new ReflectionClass($senderClass);
        while ($parent = $class->getParentClass()) {
            $interfaces = $parent->getInterfaceNames();
            if (is_array($interfaces) && array_search( 'app\\modules\\accounting\\components\\CountableInterface', $interfaces ) ) {
                $classes[] = $parent->getName();
            }
            $class = $parent;
        }

        // Traigo todos los registros que tienen configurada la clase que llama
        $configs = AccountConfig::findAll(['class'=>$classes]);
        foreach($configs as $config){
            try {
                $movement = new $config->classMovement();
                if ($movement instanceof BaseMovement) {
                    $movement_id = $movement->move($action, $event->sender, $config);
                    if($movement_id) {
                        AccountMovementRelationManager::save($movement_id, $event->sender);
                    } else {
                        \Yii::info('---------------- MOVIMIENTO NO PUEDE SER CREADO: ' . $senderClass . ' ---  ACCION: '. $action .' ---- KEY: '.$event->sender->primaryKey, 'account-movement');
                    }
                }
            } catch(\Exception $ex){
                \Yii::info('---------------- MODELO: ' . $senderClass . ' ---  ACCION: '. $action .' ---- KEY: '.$event->sender->primaryKey .' --- '.$ex->getMessage() . ' - '. $ex->getTraceAsString(), 'account-movement');
                throw $ex;
            }
        }
    }
}