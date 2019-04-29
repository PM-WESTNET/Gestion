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
use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use Yii;

class ProviderBillMovement extends BaseMovement
{

    /**
     * @param $action string insert o update
     * @param $modelInstance ProviderBill Instancia del modelo
     * @param $accountConfig AccountConfig
     * @return mixed
     */
    public function move($action, $modelInstance, $accountConfig)
    {

        try {

            // Si la factura esta cerrada, verifico si se esta guardando, actualizando o borrando
            if ($modelInstance->status == "closed") {

                // Solo hago los movimientos si se esta actualizando.
                if ($action == "update") {
                    $amounts = $modelInstance->getAmounts();

                    $accountConfigHasAccounts = $accountConfig->accountConfigHasAccounts;
                    $items = array();
                    $debit = 0;
                    $credit = 0;

                    // Itero los items.
                    if (array_key_exists('items', $amounts) !== false) {
                        $bill_items = $amounts['items'];
                        foreach ($bill_items as $account_id => $amount) {

                            if (array_key_exists($account_id, $items) !== false ) {
                                $item = $items[$account_id];
                            } else {
                                $item = new AccountMovementItem();
                                $item->account_id = $account_id;
                            }

                            if ($modelInstance->billType->multiplier) {
                                $item->debit += $amount;
                                $item->credit = 0;
                            } else {
                                $item->credit += $amount;
                                $item->debit = 0;
                            }
                            $debit += (array_key_exists($account_id, $items) !== false ? $amount : $item->debit);
                            $credit += (array_key_exists($account_id, $items) !== false ? $amount : $item->credit);
                            $items[$account_id] = $item;
                        }
                    }
                    // Itero en los montos
                    foreach ($amounts as $key => $amount) {
                        $value = 0;
                        $account_id = 0;
                        // Si es el total,se lo creo a la empresa
                        if ($key == 'total') {
                            if ($modelInstance->provider->account) {
                                $account_id = $modelInstance->provider->account->account_id;
                            } else {

                                foreach ($accountConfigHasAccounts as $aca) {
                                    if ($aca->attrib == 'total') {
                                        $account_id = $aca->account_id;
                                        break;
                                    }
                                }
                            }
                            $value = $amount;
                            $isDebit = !($modelInstance->billType->multiplier);
                        } elseif ($key == 'items') {

                        } else if (is_array($amount) && $key != 'items') {
                            // Solo pasa si es impuesto.
                            // Se puede dar el problema de que el impuesto no este configurado, y al no encontrarlo
                            // lo asigne a cualquier otra cuenta, Deberia de ser a la de resto, se arregla mas abajo.
                            $value = $amount['amount'];
                            foreach ($accountConfigHasAccounts as $aca) {
                                if ($aca->attrib == $key) {
                                    $account_id = $aca->account_id;

                                    break;
                                }
                            }
                            $isDebit = ($modelInstance->billType->multiplier);
                        }

                        if ($account_id && $value) {
                            // Verifico si el account_id ya esta con debe o haber

                            if (array_key_exists($account_id, $items) !== false) {
                                /*if( ($isDebit && ($items[$account_id]->credit != 0 )) ||
                                    (!$isDebit && ($items[$account_id]->debit != 0 ) ) ) {
                                    $item = new AccountMovementItem();
                                    $item->account_id = $account_id;
                                } else {*/
                                    $item = $items[$account_id];
                                //}
                            } else {
                                $item = new AccountMovementItem();
                                $item->account_id = $account_id;
                            }
                            // Si es Una compra
                            if ($isDebit) {
                                $item->debit += $value;
                                //$item->credit = 0;
                            } else {
                                $item->credit += $value;
                                //$item->debit = 0;
                            }
                            $debit += (array_key_exists($account_id, $items) !== false && $isDebit ? $value : $item->debit);
                            $credit += (array_key_exists($account_id, $items) !== false && !$isDebit ? $value : $item->credit);
                            $items[$account_id] = $item;
                            $account_id = null;
                        }
                    }

                    // Si tengo diferencia, puede ser por impuestos no imputados, o por items sin cuenta.
                    // En ambos casos tienen que ir a la cuenta de resto de la configuracion.
                    // A esta altura el debito o credito tienen que ser igual al total, de ser asi hay que agregar
                    // un item con el resto con saldo del menor.
                    $onDebit = ($debit != $amounts['total']);
                    $rest = $amounts['total'] - $debit;
                    $item = new AccountMovementItem();

                    if ($rest > 0) {
                        foreach ($accountConfigHasAccounts as $key => $value) {
                            if ($value->attrib == 'rest') {
                                if (array_key_exists($value->account_id, $items) !== false) {
                                    $item = $items[$value->account_id];
                                } else {
                                    $item = new AccountMovementItem();
                                    $item->account_id = $value->account_id;
                                }
                                // Si es Una compra
                                if ($modelInstance->billType->multiplier && $value->is_debit) {
                                    $item->debit += $rest;
                                    $item->credit = 0;
                                } else {
                                    $item->credit += $rest;
                                    $item->debit = 0;
                                }
                                $items[$item->account_id] = $item;
                                $debit += $item->debit;
                                $credit += $item->credit;
                                break;
                            }
                        }
                    }
                    //error_log(print_r($items,1));

                    $countMov = CountableMovement::getInstance();
                    if( !( $account_movement_id = $countMov->createMovement(
                            Yii::t('app', 'Bill') . " - " . (trim($modelInstance->description) != '' ? $modelInstance->description . ' - ' : '') . $modelInstance->provider->name,
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
                $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage());
            } else {
                echo Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage();
            }
        }
        return false;
    }
}
