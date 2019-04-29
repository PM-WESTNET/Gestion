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
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\provider\models\ProviderPayment;
use Yii;

class ProviderPaymentMovement extends BaseMovement
{

    /**
     * @param $action string insert o update
     * @param $modelInstance object Instancia del modelo
     * @param $accountConfig AccountConfig
     * @return mixed
     */
    public function move($action, $modelInstance, $accountConfig)
    {
        try{
            if ($modelInstance instanceof ProviderPayment) {
                if ($action == "update" && $modelInstance->status == "closed") {
                    $amounts = $modelInstance->getAmounts();

                    $itemsPassed = [];
                    $items = [];
                    // Creo los movimientos
                    foreach ($accountConfig->accountConfigHasAccounts as $aca) {
                        // Si es cuenta corriente no lo registro
                        if (array_key_exists($aca->attrib, $amounts)) {
                            foreach ($amounts[$aca->attrib] as $amount) {

                                if ($amount > 0) {
                                    // Para debito busco la cuenta del customer
                                    $account = $aca->account;
                                    if (!$aca->is_debit) {
                                        // En ambos casos la cuenta se utiliza para reemplazar a las de haber
                                        // Si tiene una cuenta de banco asignada
                                        foreach ($modelInstance->providerPaymentItems as $pItem) {
                                            if (!array_key_exists($pItem->provider_payment_item_id, $itemsPassed) && $pItem->payment_method_id == $aca->attrib) {
                                                if (!is_null($pItem->money_box_account_id)) {
                                                    $account = $pItem->moneyBoxAccount->account;
                                                    $itemsPassed[$pItem->provider_payment_item_id] = $pItem;
                                                    break;
                                                } else if ($pItem->paycheck) {
                                                    // Si tiene un cheque asignado, hay que ver si es propio o no.
                                                    // Si es propio
                                                    if ($pItem->paycheck->checkbook) {
                                                        $account = $pItem->paycheck->checkbook->moneyBoxAccount->account;
                                                        $itemsPassed[$pItem->provider_payment_item_id] = $pItem;
                                                        break;
                                                    }
                                                }

                                            }
                                        }
                                    }
                                    $item = new AccountMovementItem();
                                    if ($aca->is_debit) {
                                        $item->account_id = ($modelInstance->provider->account ? $modelInstance->provider->account->account_id : $account->account_id);
                                        $item->debit = $amount;
                                        $item->credit = 0;
                                    } else {
                                        $item->account_id = $account->account_id;
                                        $item->credit = $amount;
                                        $item->debit = 0;
                                    }
                                    if (array_key_exists($item->account_id, $items)) {
                                        $items[$item->account_id]->debit += $item->debit;
                                        $items[$item->account_id]->credit += $item->credit;
                                    } else {
                                        $items[] = $item;
                                    }
                                }
                            }
                        }
                    }
                    $countMov = CountableMovement::getInstance();
                    if( !( $account_movement_id = $countMov->createMovement(Yii::t('app', 'Provider Payment') . " - " . $modelInstance->description . " - " . $modelInstance->provider->name,
                        $modelInstance->company_id,
                        $items,
                        null,
                        $modelInstance->partner_distribution_model_id,
                        $modelInstance->date )) ) {
                        $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                        foreach($countMov->getErrors() as $error) {
                            Yii::$app->session->addFlash('error', $error);
                            $this->addMessage('error', $error);
                        }
                    } else {
                        return $account_movement_id;
                    }
                }
            }

        } catch (\Exception $ex) {
            if (Yii::$app instanceof \yii\web\Application) {
                $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.') .
                    $ex->getMessage()
                );
            } else {
                echo Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage();
            }
        }
        return false;

    }
}