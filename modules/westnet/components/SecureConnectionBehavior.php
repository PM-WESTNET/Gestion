<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/01/16
 * Time: 14:55
 */

namespace app\modules\westnet\components;


use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\westnet\models\Connection;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class SecureConnectionBehavior extends Behavior
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

    public function afterUpdate($event)
    {
        if($event->sender instanceof Connection) {
            $contract = $event->sender->contract;
            $connection = $event->sender;
        } else if( $event->sender instanceof Contract ) {
            $contract = $event->sender;
            $connection = Connection::findOne(['contract_id'=>$contract->contract_id]);
            if(!$connection) {
                $connection = new Connection();
                $connection->contract_id = $contract->contract_id;
                $connection->status_account = Connection::STATUS_ACCOUNT_DISABLED;
                $connection->due_date = null;
                $connection->setScenario(Connection::SCENARIO_NEW);
                $connection->save();
            }
        }

        // Solo actualizo si el contrato no es borrador y estoy deshabilitando la conexion
        if(!YII_ENV_TEST && $contract->status != Contract::STATUS_DRAFT &&
            ( $connection->status_account == Connection::STATUS_ACCOUNT_DISABLED ||
              $connection->status_account == Connection::STATUS_ACCOUNT_ENABLED   ) || 
            $connection->isNodeChanged() || $connection->clean
        ){
            SecureConnectionUpdate::update($connection, $contract, (!$event->sender instanceof Connection));
        }
    }
}