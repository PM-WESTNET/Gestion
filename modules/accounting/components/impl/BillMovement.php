<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/07/15
 * Time: 15:03
 */

namespace app\modules\accounting\components\impl;

use app\modules\accounting\components\BaseMovement;
use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountConfig;
use app\modules\accounting\models\AccountMovementItem;
use Yii;
use yii\console\Application;

class BillMovement extends BaseMovement
{

    /**
     * @param $action string insert o update
     * @param $modelInstance object Instancia del modelo
     * @param $accountConfig AccountConfig
     * @return mixed
     */
    public function move($action, $modelInstance, $accountConfig)
    {
        try {
            // Si la factura esta cerrada efectuo los movimientos.
            if ($action == "update" && $modelInstance->status == "completed" /*&&
                ( ( $modelInstance->billType->invoiceClass !== null && trim($modelInstance->ein) != "" ) ||
                    $modelInstance->billType->invoiceClass === null ) */) {

                $amounts = $modelInstance->getAmounts();
                $items = array();
                // Creo los movimientos generales para una factura, no se incluyen distincion por tipo de comprobante.
                foreach ($accountConfig->accountConfigHasAccounts as $aca) {
                    if (array_key_exists($aca->attrib, $amounts)) {
                        if (array_key_exists($aca->attrib, $amounts) && is_array($amounts[$aca->attrib])) {
                            $amount = $amounts[$aca->attrib]['amount'];
                        } else {
                            $amount = $amounts[$aca->attrib];
                        }
                        // Para debito busco la cuenta del customer
                        $account = $aca->account;
                        if ($aca->is_debit) {
                            if (isset($modelInstance->customer->account)) {
                                $account = $modelInstance->customer->account;
                            }
                        }

                        $item = new AccountMovementItem();
                        $item->account_id = $account->account_id;

                        if ($aca->is_debit && $modelInstance->billType->multiplier) {
                            $item->debit = $amount;
                            $item->credit = 0;
                        } else {
                            $item->credit = $amount;
                            $item->debit = 0;
                        }

                        $items[] = $item;
                    }
                }

                $countMov = CountableMovement::getInstance();
                if( !( $account_movement_id = $countMov->createMovement((($modelInstance->billType ? $modelInstance->billType->name . " - " . $modelInstance->number : "") . ' .Fecha del comprobante: ' . $modelInstance->date),
                        $modelInstance->company_id,
                        $items,
                        null,
                        $modelInstance->partner_distribution_model_id )) ) {

                    $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                    foreach($countMov->getErrors() as $error) {
                        $this->addMessage('error', $error);
                    }
                } else {
                    return $account_movement_id;
                }
            }

        } catch(\Exception $ex) {
            if (Yii::$app instanceof \yii\web\Application) {
                $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage());
            } else {
                echo Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage();
            }
        }
        return false;
    }
}