<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/07/15
 * Time: 15:03
 */

namespace app\modules\accounting\components\impl;


use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\BaseMovement;
use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountConfig;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentReceipt;
use Yii;

class PaymentMovement extends BaseMovement
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
            if ($action == "update") {

                // Si es una instancia de Payment
                if (($modelInstance instanceof Payment )) {
                    $countMov = CountableMovement::getInstance();

                    // Si lo estoy cancelando hago los movimientos inversos.
                    if( $modelInstance->status == 'cancelled') {
                        $amr = AccountMovementRelationManager::find($modelInstance);

                        if( !($account_movement_id = $countMov->revertMovement($amr->account_movement_id) ) ) {
                            $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.'));
                            foreach($countMov->getErrors() as $error) {
                                $this->addMessage('error', $error);
                            }
                        } else {
                            return $account_movement_id;
                        }

                    } else if( $modelInstance->status == 'closed') {
                        $amounts = $modelInstance->getAmounts();

                        // Creo los movimientos
                        $items = array();
                        $debit = 0; $credit = 0;
                        $accountConfigHasAccounts = $accountConfig->accountConfigHasAccounts;

                        foreach( $modelInstance->paymentItems as $key => $paymentItem ){
                            $account = null;

                            $amount = $paymentItem->amount;

                            // Verifico si es un cheque o una cuenta bancaria
                            if (!empty($paymentItem->paycheck_id)) {
                                // Traigo la cuenta del  banco del cheque
//                            $account = $paymentItem->paycheck->moneyBox->account;
                            } else if (!empty($paymentItem->money_box_account_id)) {
                                // Si es una cuenta bancaria,
                                if($paymentItem->moneyBoxAccount->account){
                                    $account = $paymentItem->moneyBoxAccount->account;
                                } else if ($paymentItem->moneyBoxAccount->moneyBox->account) {
                                    $account = $paymentItem->moneyBoxAccount->moneyBox->account;
                                }
                            }
                            $is_debit = true;
                            // Si no encuentro cuenta busco en la configuracion
                            if(is_null($account)) {
                                foreach ($accountConfigHasAccounts as $accountConfig) {
                                    if($accountConfig->attrib == $paymentItem->payment_method_id) {
                                        $amount = $amounts[$accountConfig->attrib];
                                        $account = $accountConfig->account;
                                        $is_debit = $accountConfig->is_debit;
                                    }
                                }
                            }

                            if(!is_null($account)) {
                                // Si existe la cuenta creo todo, sino se creara cusando la configuracion
                                if (array_key_exists($account->account_id, $items)) {
                                    $item = $items[$account->account_id];
                                    if ($is_debit) {
                                        $item->debit += $amount;
                                        $item->credit = 0;
                                    } else {
                                        $item->credit += $amount;
                                        $item->debit = 0;
                                    }
                                } else {
                                    $item = new AccountMovementItem();
                                    $item->account_id = $account->account_id;
                                    if ($is_debit) {
                                        $item->debit = $amount;
                                        $item->credit = 0;
                                    } else {
                                        $item->credit = $amount;
                                        $item->debit = 0;
                                    }

                                }
                                $items[$account->account_id] = $item;
                                $debit  = (array_key_exists($account->account_id, $items)!==false && $is_debit ? $item->debit + $amount : $item->debit );
                                $credit = (array_key_exists($account->account_id, $items)!==false && !$is_debit ? $item->credit + $amount : $item->credit );
                            }
                        }


                        // Me puede quedar alguna otra configuracion sin agregar.
                        // En el caso de que la cuenta ya este agregada a los items, no se agrega.

                        if($debit!=$credit) {
                            foreach ($accountConfigHasAccounts as $accountConfig) {
                                if (array_key_exists($accountConfig->account_id, $items) !== false) {
                                    $item = $items[$accountConfig->account_id];
                                } else {
                                    $item = new AccountMovementItem();
                                    $item->account_id = $accountConfig->account_id;
                                }
                                if(($credit < $modelInstance->amount && !$accountConfig->is_debit ) ||
                                    ($debit < $modelInstance->amount && $accountConfig->is_debit)) {
                                    $amount = $amounts[$accountConfig->attrib];

                                    if($accountConfig->is_debit){
                                        $item->debit += $amount;
                                        $item->credit = 0;
                                        $items[$accountConfig->account_id] = $item;
                                        $debit = (array_key_exists($accountConfig->account_id, $items)!==false ? $item->debit + $amount : $item->debit );
                                    } else {
                                        $item->credit += $amount;
                                        $item->debit = 0;
                                        $items[$accountConfig->account_id] = $item;
                                        $credit = (array_key_exists($accountConfig->account_id, $items)!==false ? $item->credit + $amount : $item->credit );
                                    }
                                }
                            }
                        }

                        if( !($account_movement_id = $countMov->createMovement(
                                Yii::t('app',  'Charge') . " - " . $modelInstance->concept . " - " . $modelInstance->customer->getFullName() . ' .Fecha del comprobante: ' . $modelInstance->date,
                                $modelInstance->company_id,
                                $items,
                                null,
                                $modelInstance->partner_distribution_model_id
                            )) ) {
                            $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                            foreach($countMov->getErrors() as $error) {
                                $this->addMessage('error', $error);
                            }

                        } else {
                            return $account_movement_id;
                        }
                    }
                }
            }
        }catch(\Exception $ex) {
            if (Yii::$app instanceof \yii\web\Application) {
                $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage());
            } else {
                echo Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage();
            }
        }
        return false;
    }
}
