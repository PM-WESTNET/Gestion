<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/09/16
 * Time: 14:46
 */

namespace app\modules\partner\components;


use app\modules\accounting\models\AccountConfig;
use app\modules\accounting\models\AccountingPeriod;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\checkout\models\Payment;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\partner\models\PartnerMovement;
use app\modules\sale\models\Company;
use Yii;

class Movement
{
    public $error = [];

    /**
     * Genera un movimiento de aporte de dinero por parte de un socio.
     *
     * @param PartnerMovement $partnerMovement
     * @return array|bool
     */
    public function input(PartnerMovement $partnerMovement)
    {
        // Trato los ingresos como si fuese un cobro, asi puedo sacar la configuracion de cuentas de ahi.
        $config = AccountConfig::findOne(['class'=>'app\\modules\\checkout\\models\\Payment']);

        $movement = new AccountMovement();
        $movement->description = Yii::t('partner', 'Input Movement') . " - " .  $partnerMovement->description;
        $movement->status = 'draft';
        $movement->date = $partnerMovement->date;
        $movement->time = (new \DateTime('now'))->format('H:i:s');
        $movement->company_id = $partnerMovement->company_id;
        $movement->accounting_period_id = AccountingPeriod::getActivePeriod()->accounting_period_id;
        $movement->partner_distribution_model_id = $this->findPartnerDistributionModel($partnerMovement->company_id, $partnerMovement->partner_id);

        if ( $movement->validate() ) {
            $movement->save();

            // Si es efectivo va a una caja, con money_box_account_id
            $item = new AccountMovementItem();
            if (!empty($partnerMovement->paycheck_id)) {
            } else if (!empty($partnerMovement->money_box_account_id)) {
                // Si es una cuenta bancaria,
                if($partnerMovement->moneyBoxAccount->account){
                    $item->account_id = $partnerMovement->moneyBoxAccount->account_id;
                } else if ($partnerMovement->moneyBoxAccount->moneyBox->account) {
                    $item->account_id = $partnerMovement->moneyBoxAccount->moneyBox->account_id;
                }
            }
            // Si no encuentro cuenta busco en la configuracion
            if(is_null($item->account_id)) {
                foreach ($config->accountConfigHasAccounts as $accountConfig) {
                    if($accountConfig->attrib == $partnerMovement->payment_method_id) {
                        $item->account_id = $accountConfig->account_id;
                    }
                }
            }
            $item->debit = $partnerMovement->amount;
            $item->status = 'draft';
            $item->account_movement_id = $movement->account_movement_id;
            $item->save();

            $item = new AccountMovementItem();
            $item->account_id = $partnerMovement->getPartner()->account_id;
            $item->credit = $partnerMovement->amount;
            $item->status = 'draft';
            $item->account_movement_id = $movement->account_movement_id;
            $item->save();

            return true;
        } else {
            $this->error[] = 'The movement is invalid.';
            return false;
        }
    }

    public function withDraw(PartnerMovement $partnerMovement)
    {
        // Trato los ingresos como si fuese un cobro, asi puedo sacar la configuracion de cuentas de ahi.
        $config = AccountConfig::findOne(['class'=>'app\\modules\\provider\\models\\ProviderPayment']);

        $movement = new AccountMovement();
        $movement->description = Yii::t('partner', 'Withdraw Movement') . " - " .  $partnerMovement->description;
        $movement->status = 'draft';
        $movement->date = $partnerMovement->date;
        $movement->time = (new \DateTime('now'))->format('H:i:s');
        $movement->company_id = $partnerMovement->company_id;
        $movement->accounting_period_id = AccountingPeriod::getActivePeriod()->accounting_period_id;
        $movement->partner_distribution_model_id = $this->findPartnerDistributionModel($partnerMovement->company_id, $partnerMovement->partner_id);

        if ( $movement->validate() ) {
            $movement->save();

            // Si es efectivo va a una caja, con money_box_account_id
            $item = new AccountMovementItem();
            if (!empty($partnerMovement->paycheck_id)) {
            } else if (!empty($partnerMovement->money_box_account_id)) {
                // Si es una cuenta bancaria,
                if($partnerMovement->moneyBoxAccount->account){
                    $item->account_id = $partnerMovement->moneyBoxAccount->account_id;
                } else if ($partnerMovement->moneyBoxAccount->moneyBox->account) {
                    $item->account_id = $partnerMovement->moneyBoxAccount->moneyBox->account_id;
                }
            }
            // Si no encuentro cuenta busco en la configuracion
            if(is_null($item->account_id)) {
                foreach ($config->accountConfigHasAccounts as $accountConfig) {
                    if($accountConfig->attrib == $partnerMovement->payment_method_id) {
                        $item->account_id = $accountConfig->account_id;
                    }
                }
            }
            $item->credit = $partnerMovement->amount;
            $item->status = 'draft';
            $item->account_movement_id = $movement->account_movement_id;
            $item->save();

            $item = new AccountMovementItem();
            $item->account_id = $partnerMovement->getPartner()->account_id;
            $item->debit = $partnerMovement->amount;
            $item->status = 'draft';
            $item->account_movement_id = $movement->account_movement_id;
            $item->save();

            return true;
        } else {
            $this->error[] = 'The movement is invalid.';
            return false;
        }
    }

    private function findPartnerDistributionModel($company_id, $partner_id)
    {
        $partners = PartnerDistributionModel::find()->andWhere(['company_id'=>$company_id])->all();
        /** @var PartnerDistributionModel $partner */
        foreach ($partners as $partner) {

            foreach ( $partner->partnerDistributionModelHasPartner as $models ) {
                if( $models->percentage == 100 && $models->partner_id == $partner_id ) {
                    return $models->partner_distribution_model_id;
                }
            }
        }
        return Company::findOne(['company_id'=>$company_id])->partner_distribution_model_id;
    }
}