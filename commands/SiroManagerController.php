<?php

namespace app\commands;

use yii\console\Controller;
use Yii;

use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;
use app\modules\alertsbot\controllers\TelegramController;
use app\modules\westnet\notifications\models\SiroCompanyConfig;
use app\modules\westnet\notifications\models\SiroPaymentIntention;

class SiroManagerController extends Controller{

    /**
     * this function is intended to close all payment intentions that go over the limit lifespan of about 10-15 minutes
     * siro-manager/close-payment-intention
     */
    public function actionClosePaymentIntention()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->stdout("\nactionClosePaymentIntention() start\n");
            $unclosedPaymentIntentions = SiroPaymentIntention::find()
                ->where(['status' => 'pending']) // get all STILL pending
                ->orderBy(['siro_payment_intention_id' => SORT_ASC]) //gets the first records first
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

    /**
     * NEW
     * this is a merge from previous action to check payments and duplicates,
     * in a hope for it to be more efficient and readable.
     * 
     * This action is triggered by a system task (cronjob) and logs to /var/log/siro-revise-payments.log
     * siro-manager/payments-revisor 1
     */
    public function actionPaymentsRevisor($save = false)
    {
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
        $this->stdout("\n----SIRO SINGLE PAYMENTS REVISOR INITIATED (SINGLE + DUPLICATE CHECKER)---- ".date("Y-m-d H:i:s")."\n");

        // get enabled companies ids
        $siro_enabled_companies = SiroCompanyConfig::getEnabledCompaniesIds();

        // get company models
        $companies = Company::find()->where(['in', 'company_id', $siro_enabled_companies])->all();

        if(!empty($companies)){
            foreach ($companies as $company) {
                $this->reviseAllPayments($company, $save);
            }
        }else{
            $this->stdout("\n----ERROR : ENABLED COMPANIES NOT FOUND FOR THIS SERVICE----"."\n");

        }
        $this->stdout("\n----END---- ".date("Y-m-d H:i:s")."\n");
	     
    }

    /**
     * 
     */
    private function reviseAllPayments($company, $save){
        $this->stdout("INFO\n");
        $this->stdout("Company: ".$company->name."\n");

        // start process of payment revision 
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // hardcoded date range to revise ->
            $from_date = date('Y-m-d', strtotime("-2 months"));
            $to_date = date('Y-m-d');
            // excecute revisor 
            $ok = PaymentIntentionAccountability::revisePaymentsProcess($company, $from_date, $to_date);
            // echo result
            if($ok){
                echo "Process ok!\n";
            }else{
                echo "Err. Something went wrong.\n";
            }

            // transaction commit
            if($save){
                $transaction->commit();
            }else{
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $errorMsg = "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            // "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n";
            $this->stdout($errorMsg);
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_cron.txt'),
            $errorMsg,
            FILE_APPEND);
            $transaction->rollBack();

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch (ROLLBACK DONE): siro-manager/payments-revisor ****', $e);
        }
            
    }


}