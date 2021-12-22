<?php
namespace app\modules\westnet\commands;
use yii\console\Controller;
use app\modules\westnet\notifications\models\SiroPaymentIntention;


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
        $this->stdout("\nactionClosePaymentIntention() start\n");
        $unclosedPaymentIntentions = SiroPaymentIntention::find()
                                    ->where(['status' => 'pending']) // get all STILL pending
                                    ->limit(5) // limit as not to overload the server (very unlikely)
                                    ->all(); // get all records

        $this->stdout("\nQuery hit count: ".count($unclosedPaymentIntentions)."\n");

        foreach ($unclosedPaymentIntentions as $paymentIntention){
            $this->stdout("payment id: $paymentIntention->siro_payment_intention_id \n");
            $this->stdout("$paymentIntention->createdAt\n");
            $minutes_to_add = 15;
            $payIntentionDate = new \DateTime($paymentIntention->createdAt); //
            $lifespanLimit = $payIntentionDate->add(new \DateInterval('PT' . $minutes_to_add . 'M')); // we are providing PT15M (or 15 minutes) to the DateInterval constructor.
            //$lifespanLimit=$paymentIntention->createdAt->add(strtotime('+15 minutes'));
            $this->stdout("lifespanLimit??".$lifespanLimit->format('Y-m-d H:i:s')."\n"); // TODO: save lifespan to db actually, because this doesnt make any sense

            // TODO: check if payment intention is past its lifespan of 15 minutes
            
            // TODO: send payment intention to cancelled if it past

            // TODO: do nothing if not.

        }
        $date = date('Y-m-d H:i:s', strtotime('now +15 minutes'));
        $this->stdout("\n$date\n");
        //$this->stdout(strtotime('now +15 minutes')."\n");
        //$this->stdout(var_export($unclosedPaymentIntentions, true));
        
    }

}