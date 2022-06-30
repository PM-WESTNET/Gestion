<?php

namespace app\modules\westnet\commands;

use yii\console\Controller;
use app\modules\westnet\notifications\models\SiroPaymentIntention;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Customer;
use app\modules\alertsbot\controllers\TelegramController;

/**
 * Class SiroController
 * This controller represents all actions done via console commands and cronjobs.
 */
class SiroController extends Controller
{
    /**
     * this function is intended to close all payment intentions that surpass a static lifespan of about 10-15 minutes
     */
    public function actionClosePaymentIntention()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->stdout("\nactionClosePaymentIntention() start\n");
            $unclosedPaymentIntentions = SiroPaymentIntention::find()
                ->where(['status' => 'pending']) // get all STILL pending
                ->orderBy(['siro_payment_intention_id' => SORT_ASC]) //gets the first records first
                //->limit(1000) // limit as not to overload the server (very unlikely)
                ->all(); // get all records

            $this->stdout("\nQuery hit count: " . count($unclosedPaymentIntentions) . "\n");

            foreach ($unclosedPaymentIntentions as $paymentIntention) {
                $this->stdout("\npayment id: $paymentIntention->siro_payment_intention_id \n");

                $current_date = strtotime(date("d-m-Y H:i:00", time()));
                $payment_date = strtotime($paymentIntention->createdAt);
                $expiry_time = (int)Config::getConfig('siro_expiry_time')->item->description * 60; // small calc to get the minute integer

                $this->stdout("created at: \t\t" . date('d-m-Y H:i:00', $payment_date) . "\n");
                $this->stdout("lifespan limit: \t" . date('d-m-Y H:i:00', ($payment_date + $expiry_time)) . "\n");

                if ($current_date > ($payment_date + $expiry_time)) { // if the current date is smaller than the lifespan limit
                    $this->stdout("Must close this payment intention\n");
                    $paymentIntention->status = 'canceled';
                    if ($paymentIntention->save(true, ['status'])) {
                        $this->stdout("payment intent saved\n");
                    } else {
                        $this->stdout("payment intent didnt save\n");
                        $this->stdout(var_export($paymentIntention->getErrorSummary(true)) . "\n");
                    }
                } else {
                    $this->stdout("This payment intention is still valid\n");
                }
                $this->stdout("\n");
            }
            $this->stdout("Finished switching state for siro payment intentions\n");
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->stdout("Errors..\n");
            $this->stdout(var_export($ex, true));

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch (ROLLBACK DONE): westnet/siro/close-payment-intention ****', $ex);

        }
    }
}
