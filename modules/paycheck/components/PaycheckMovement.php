<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 4/10/16
 * Time: 12:29
 */

namespace app\modules\paycheck\components;


use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\BaseMovement;
use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountConfig;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\accounting\models\AccountMovementRelation;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\config\models\Config;
use app\modules\paycheck\models\Paycheck;
use app\modules\sale\models\Company;
use Codeception\Util\Debug;
use Yii;

class PaycheckMovement extends BaseMovement
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
            if ($action == "update" && $modelInstance->status == Paycheck::STATE_DEPOSITED ) {
                $config = AccountConfig::findOne(['class' => 'app\\modules\\checkout\\models\\Payment']);
                $amounts = $modelInstance->getAmounts();

                $items = array();

                $item = new AccountMovementItem();
                $item->account_id = $modelInstance->moneyBoxAccount->account_id;
                $item->debit = $amounts['total'];
                $item->status = AccountMovementItem::STATE_DRAFT;
                $items[] = $item;

                foreach ($config->accountConfigHasAccounts as $aca) {
                    if ($aca->attrib == Config::getValue('payment_method_paycheck')) {
                        $item = new AccountMovementItem();
                        $item->account_id = $aca->account_id;
                        $item->credit = $amounts['total'];
                        $item->status = AccountMovementItem::STATE_DRAFT;
                        $items[] = $item;
                    }
                }

                $company = Company::findOne(['company_id' => Config::getValue("ecopago_batch_closure_company_id")]);
                $countMov = CountableMovement::getInstance();
                if (!($account_movement_id = $countMov->createMovement(Yii::t('paycheck', 'Deposit') . " - " . Yii::t('paycheck', 'Paycheck') . ": " . $modelInstance->moneyBox->name . " Nro.: " . $modelInstance->number,
                    $company->company_id,
                    $items,
                    null,
                    $company->partner_distribution_model_id,
                    (new \DateTime($modelInstance->dateStamp))->format('d-m-Y')))
                ) {
                    $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                    foreach ($countMov->getErrors() as $error) {
                        $this->addMessage('error', $error);
                    }
                } else {
                    return $account_movement_id;
                }
            }/**elseif ($action == "update" && $modelInstance->status == Paycheck::STATE_COMMITED && $modelInstance->is_own = 1 ){
                //$config = AccountConfig::findOne(['class' => 'app\\modules\\paycheck\\models\\Paycheck']);
                $amounts = $modelInstance->getAmounts();
                $items = array();

                $item = new AccountMovementItem();
                $item->account_id = $modelInstance->moneyBoxAccount->account_id;
                $item->debit = $amounts['total'];
                $item->status = AccountMovementItem::STATE_DRAFT;
                $items[] = $item;

                $item = new AccountMovementItem();
                $item->account_id = $modelInstance->outAccount;
                $item->credit = $amounts['total'];
                $item->status = AccountMovementItem::STATE_DRAFT;
                $items[] = $item;

                $company = $modelInstance->moneyBoxAccount->company;
                $countMov = CountableMovement::getInstance();
                if (!($account_movement_id = $countMov->createMovement(Yii::t('paycheck', 'Payment') . " - " . Yii::t('paycheck', 'Paycheck') . ": " . $modelInstance->moneyBoxAccount->moneyBox->name . " Nro.: " . $modelInstance->number,
                    $company->company_id,
                    $items,
                    null,
                    $company->partner_distribution_model_id,
                    (new \DateTime($modelInstance->dateStamp))->format('d-m-Y')))
                ) {
                    $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                        Debug::debug('Error en countMov');
                    foreach ($countMov->getErrors() as $error) {
                        $this->addMessage('error', $error);
                    }
                } else {
                    Debug::debug('Por crear relacion');
                    $relation = new AccountMovementRelation([
                        'class' => 'app\modules\paycheck\models\Paycheck',
                        'model_id' => $modelInstance->paycheck_id,
                        'account_movement_id' => $account_movement_id
                    ]);

                    $relation->save();
                    Debug::debug($account_movement_id);
                    return $account_movement_id;
                }

            }**/ elseif ($action == "update" && $modelInstance->status == Paycheck::STATE_REJECTED ) {
                /** @var AccountMovementRelation $amr */
                $amr = AccountMovementRelationManager::find($modelInstance);
                return CountableMovement::getInstance()->revertMovement($amr->account_movement_id, Yii::t('paycheck', 'Paycheck') . " - " . Yii::t('paycheck', 'rejected') );

            }
        } catch (\Exception $ex) {
            Debug::debug($ex);
            if (Yii::$app instanceof \yii\web\Application) {
                $this->addMessage('error', Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage());
            } else {
                echo Yii::t('accounting', 'The movement could not be created.') . $ex->getMessage();
            }
        }
        Debug::debug('Salio por false');
        return false;
    }
}