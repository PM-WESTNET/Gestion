<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 6/09/17
 * Time: 16:18
 */

namespace app\modules\sale\modules\contract\components;


use app\modules\config\models\Config;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\models\Connection;
use Exception;
use Yii;

class ContractLowService
{

    /**
     * @param Contract $contract
     * @param $date
     * @param $category_low_id
     */
    public function startLowProcess($contract, \DateTime $date, $category_low_id=null, $create_credit = false)
    {
        if ($contract->status === Contract::STATUS_ACTIVE) {
            $transaction= \Yii::$app->getDb()->beginTransaction();

            if($date > (new \DateTime('now'))) {
                $fields[] = 'low_date';
                $contract->low_date = $date->format('Y-m-d');
                if($category_low_id) {
                    $contract->category_low_id = $category_low_id;
                    $fields[] = 'category_low_id';
                }
                if ($contract->updateAttributes($fields)) {
                    $transaction->commit();
                    //Deshabulito firstdata
                    $contract->customer->inactiveFirstdataDebit();
                    //Creo nota de credito por la deuda del cliente
                    if ($create_credit) {
                        if (!$contract->customer->createCreditForDebt()) {
                            Yii::$app->session->addFlash('error', Yii::t('app','Can`t create the credit note'));
                        }
                    }
                    return true;
                } else {
                    //throw new \Exception(Yii::t('app', 'Can\'t begin the low process of this contract.'));
                }
            } else {
                $contract->status = Contract::STATUS_LOW_PROCESS;
                if ($contract->updateAttributes(['status'])) {
                    $connection = Connection::findOne(['contract_id' => $contract->contract_id]);

                    $connection->status_account = Connection::STATUS_ACCOUNT_CLIPPED;

                    if ($connection->updateAttributes(['status_account'])) {
                        $this->createTicketLow($contract);
                        $transaction->commit();
                        //Deshabulito firstdata
                        $contract->customer->inactiveFirstdataDebit();
                        //Creo nota de credito por la deuda del cliente
                        if ($create_credit) {
                            if (!$contract->customer->createCreditForDebt()) {
                                Yii::$app->session->addFlash('error', Yii::t('app','Can`t create the credit note'));
                            }
                        }
                        return true;
                    } else {
                        $transaction->rollBack();
                        throw new \Exception(Yii::t('app', 'Can\'t begin the low process of this contract.')." ".'No se pudo actualizar el Estado de Cuenta del cliente.');
                    }
                } else {
                    $transaction->rollBack();
                    throw new \Exception(Yii::t('app', 'Can\'t begin the low process of this contract.')." ".'No se pudo actualizar el Contrato del cliente.');
                }
            }
        } else {
            throw new \Exception(Yii::t('app', 'Can\'t begin the low process of this contract.'));
        }
    }

    public function parseLowProcess(\DateTime $date = null)
    {
        if(!$date){
            $date = new \DateTime('now');
        }

        echo "Inicio Proceso de actualizacion de bajas - " . (new \DateTime('now'))->format('d/m/Y H:i:s')."\n";
        $contracts = Contract::findAll(['low_date'=>$date->format('Y-m-d')]);
        echo "Contratos encontrados: " . count($contracts);
        if($contracts) {
            /** @var Contract $contract */
            foreach ($contracts as $contract) {
                $this->startLowProcess($contract, $date);
            }
        }
        echo "Fin Proceso de actualizacion de bajas - " . (new \DateTime('now'))->format('d/m/Y H:i:s')."\n";
    }

    public function createTicketLow($contract)
    {
        return true;
        $category_low_id = Config::getValue('mesa_category_low_reason');
        if (!$category_low_id) {
            throw new Exception(Yii::t('app', 'Parameter not found: {parameter}', ['parameter' => 'mesa_category_low_reason']));
        }
        $ticket = new Ticket();
        $ticket->contract_id = $contract->contract_id;
        $ticket->customer_id = $contract->customer_id;
        $ticket->category_id = $category_low_id;
        $ticket->title = Yii::t('app', 'Begin Low Process');
        $ticket->content = Yii::t('app', 'Low');
        $ticket->status_id = Config::getValue('ticket_new_status_id');
        $ticket->user_id = Yii::$app->user->id;
        $ticket->setUsers([Yii::$app->user->id]);
        $ticket->save();
    }
    
}