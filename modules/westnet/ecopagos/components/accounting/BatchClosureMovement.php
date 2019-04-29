<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/07/15
 * Time: 15:03
 */

namespace app\modules\westnet\ecopagos\components\accounting;

use app\modules\accounting\components\BaseMovement;
use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\config\models\Config;
use app\modules\sale\models\Company;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\BatchClosure;
use Yii;

class BatchClosureMovement extends BaseMovement
{
    

    /**
     * @param $action string insert o update
     * @param $modelInstance BatchClosure
     * @param $accountConfig AccountConfig
     * @return mixed
     */
    public function move($action, $modelInstance, $accountConfig)
    {
        try {
            if ($action == "update" && $modelInstance->status == BatchClosure::STATUS_RENDERED) {
                $amounts = $modelInstance->getAmounts();
                $config = $modelInstance->getConfig();

                $company = Company::findOne(['company_id' => Config::getValue('ecopago_batch_closure_company_id')]);

                foreach ($amounts as $key => $amount) {

                    // Para el debe pongo la cuenta del ecopago.
                    $item = new AccountMovementItem();
                    $item->account_id = $modelInstance->getMoneyBoxAccount()->one()->account_id;

                    $item->debit = $amount;
                    $item->credit = 0;
                    $items[] = $item;

                    // Para el haber pongo la cuenta de la caja
                    $item = new AccountMovementItem();
                    //$item->account_id = 132;
                    $item->account_id = $modelInstance->ecopago->account_id;
                    $item->debit = 0;
                    $item->credit = $amount;
                    $items[] = $item;

                    $countMov = CountableMovement::getInstance();

                    if( !($account_movement_id = $countMov->createMovement(
                        EcopagosModule::t('app', 'Render of Batch Closure Nro. {number}', ['number' => $modelInstance->batch_closure_id]) . " - Ecopago: " . $modelInstance->ecopago->name,
                        $company->company_id,
                        $items,
                        null,
                        $company->partner_distribution_model_id,
                        (new \DateTime('now'))->format('Y-m-d')
                    ) ) ) {
                        $this->addMessage('error', Yii::t('accounting', 'The movement is created with errors.'));
                        foreach($countMov->getErrors() as $error) {
                            $this->addMessage('error', $error);
                        }
                    } else {
                        return $account_movement_id;
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