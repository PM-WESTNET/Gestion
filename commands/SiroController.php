<?php

namespace app\commands;

use yii\console\Controller;
use Yii;

use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;
use app\modules\westnet\notifications\models\search\PaymentIntentionAccountabilitySearch;
use app\modules\alertsbot\controllers\TelegramController;
use app\modules\westnet\notifications\models\SiroPaymentIntention;

class SiroController extends Controller{

	public function actionCheckerOfPayments()
    {
        //todo: create a model that stores company capability to run this, store its ids  like 2,7 in the westnet case, and the API consumption numbers too
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
        $this->stdout("\n----SIRO SINGLE PAYMENT CHECKER INITIATED---- ".date("Y-m-d H:i:s")."\n");
    	$companies = Company::find()->where(['in', 'company_id', [2,7]])->all();
    	foreach ($companies as $company) {
    		$this->SearchCheckerOfPayments($company);
    	}
        $this->stdout("\n----END---- ".date("Y-m-d H:i:s")."\n");
	     
    }

    public function actionFindDuplicatePayments(){
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
        $this->stdout("\n----SIRO DUPLICATE PAYMENT CHECKER INITIATED---- ".date("Y-m-d H:i:s")."\n");
    	$companies = Company::find()->where(['in', 'company_id', [2,7]])->all();

    	foreach ($companies as $company) {
    		$this->BuscarPagosDuplicados($company);
    	}
        $this->stdout("\n----END---- ".date("Y-m-d H:i:s")."\n");
    }

    private function SearchCheckerOfPayments($company){
    	//if(isset($request['buscar_pagos_duplicados']))
	      //  return $this->BuscarPagosDuplicados($request['company_id'], $request['date_from'], $request['date_to']);

        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $token = ApiSiro::getTokenApi($company->company_id);

        $from_date = date('Y-m-d', strtotime("-7 days"));
        // $from_date = date('Y-m-d', strtotime("-2 months")); // date for debugging
        $today = date('Y-m-d');
        $this->stdout("INFO\n");
        $this->stdout("Company: ".$company->name."\n");
        $this->stdout("Date range from: $from_date to: $today\n");
        
        // debug variables and data
        $debug_mode = TRUE;
        $testfile_name = 'siro-data-testing-3.txt';

        $test_data = null;
        if(file_exists($testfile_name) and $debug_mode){
            $filecontents = file_get_contents($testfile_name);
            $test_data = (json_decode($filecontents));
        }
        
        // check if test data exists. which should only if debug_mode is TRUE
        $accountability = (!is_null($test_data)) ? $test_data : ApiSiro::ObtainPaymentAccountabilityApi($token, $from_date, $today, $cuit_administrator, $company->company_id);
        // re-encode data to save into a testing file in case we need it. (if file doesnt exist, create it)
        if(((!file_exists($testfile_name)) and $debug_mode)) file_put_contents($testfile_name, json_encode($accountability));
        

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($accountability as $_index => $value) {
                if(!($_index == '446' or $_index == '838')) continue; // skip for debugging purposes

                // define empty array for intention data separation
                $reg_decoded = PaymentIntentionAccountability::decodePaymentRegisterLine($value);
                // edit and adapt data for it to be much more human-readable and have correct formatting for db.
                $payment_data = PaymentIntentionAccountability::formatPaymentData($reg_decoded);

                if(empty($payment_data['siro_payment_intention_id'])) 
                    echo "\nempty siro_payment_intention_id id at line $_index 
                        \n$value 
                        \ncustomer: ".$payment_data['customer_id']." 
                        \namount: ".$payment_data['total_amount']
                        ."\n";
                
                if($payment_data['total_amount'] > 0){
                    // var_dump('payment_data',$payment_data);
                    // echo "passes 1 $_index\n";//debugging purpose

                    // searches for a siro_payment_intention that we COULD have related to the ones from the siro API (it could be that it doesnt exist for some reason)
                    $siro_payment_intention = Yii::$app->db->createCommand(
                        'SELECT spi.estado, spi.payment_id 
                        FROM siro_payment_intention spi 
                        WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
                    ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id'])
                    ->queryOne();
                    
                    // searches for an accountability record related to the current siro_payment_intention_id
                    $payment_intention_accountability = Yii::$app->db->createCommand(
                        'SELECT pia.payment_intention_accountability_id 
                        FROM payment_intentions_accountability pia 
                        WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                    ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id'])
                    ->queryOne();

                    // var_dump('siro_payment_intention and accountability',$siro_payment_intention, $payment_intention_accountability);//debugging purpose

                    if(
                        !empty($siro_payment_intention) && // siro payment intention related record isnt empty
                        $siro_payment_intention['estado'] != SiroPaymentIntention::STATUS_PROCESSED && // its status != "PROCESADA"
                        empty($payment_intention_accountability)) // there is no accountability related record found for it
                    {
                            
                        // echo "passes 2 $_index\n";//debugging purpose

                        // continue;//debugging purpose

                        // create accountability record
                        $model = new PaymentIntentionAccountability();
                        $model->payment_date = $payment_data['payment_date'];
                        $model->accreditation_date =  $payment_data['accreditation_date'];
                        $model->total_amount =  $payment_data['total_amount'];
                        $model->customer_id = $payment_data['customer_id'];
                        $model->payment_method = $payment_data['payment_method'];
                        $model->siro_payment_intention_id = $payment_data['siro_payment_intention_id'];

                        $collection = PaymentIntentionAccountability::CODES_COLLECTION_CHANNEL[$payment_data['collection_channel']];
                        // check if code exists on db 
                        if(isset( $collection )){
                            $model->collection_channel_description = $collection;
                        }else{
                            $model->collection_channel_description = 'No se reconoce el código: ' . $payment_data['collection_channel'];
                        }

                        $model->collection_channel = $payment_data['collection_channel'];
                        $model->rejection_code = $payment_data['rejection_code'];

                        
                        if(empty($siro_payment_intention['payment_id'])){
                            $model->status = 'draft';
                            
                        }else{
                            $model->payment_id = $siro_payment_intention['payment_id'];
                            $model->status = 'payed';
                        }

                        $model->save();
                    }

                }

            }
            $transaction->rollBack(); //debugging purpose
            echo "'--end--'\n";//debugging purpose
            die();//debugging purpose
            $transaction->commit();

        } catch (\Exception $e) {
            $errorMsg = "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n";
            $this->stdout($errorMsg);
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_cron.txt'),
            $errorMsg,
            FILE_APPEND);
            $transaction->rollBack();

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch (ROLLBACK DONE): siro/checker-of-payments ****', $e);
        }
            
    }

    //todo: review and see if it can be added to checker of single payments in someway

    public function BuscarPagosDuplicados($company){
        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $token = ApiSiro::GetTokenApi($company->company_id);

        $from_date = date('Y-m-d', strtotime("-7 days"));
        $today = date('Y-m-d');
        $this->stdout("INFO\n");
        $this->stdout("Company: ".$company->name."\n");
        $this->stdout("Date range from: $from_date to: $today\n");
        
        $accountability = ApiSiro::ObtainPaymentAccountabilityApi($token, $from_date, $today, $cuit_administrator, $company->company_id);
        
        $codes_collection_channel = [
            'PF' => 'Pago Fácil',
            'RP' => 'Rapipago',
            'PP' => 'Provincia Pagos',
            'CJ' => 'Cajeros',
            'CE' => 'Cobro Express',
            'BM' => 'Banco Municipal',
            'BR' => 'Banco de Córdoba',
            'ASJ' => 'Plus Pagos',
            'LK' => 'Link Pagos',
            'PC' => 'Pago Mis Cuentas',
            'MC' => 'Mastercard',
            'VS' => 'Visa',
            'MCR' => 'Mastercard Rechazado',
            'VSR' => 'Visa Rechazado',
            'DD+' => 'Débito Directo',
            'DD-' => 'Reversión Débito',
            'DDR' => 'Rechazo Débito Directo',
            'BPD' => 'Botón de Pagos Débito',
            'BPC' => 'Botón de Pagos Crédito',
            'BOC' => 'Botón Otros Crédito',
            'BPR' => 'Botón de Pagos Rechazado',
            'CEF' => 'Cobro Express sin Factura',
            'RSF' => 'Rapipago sin Factura',
            'FSF' => 'Pago Fácil sin Factura',
            'ASF' => 'Plus Pagos sin Factura',
            'PSP' => 'Bapro sin Factura',
            'PCO' => 'PC Online',
            'LKO' => 'LK Online',
            'PCA' => 'Deuda Vencida PCO',
            'LKA' => 'Deuda Vencida LKO',
            'SNP' => 'Transferencia Inmediata.',
            'LNK' => 'Transferencia Inmediata',
            'IBK' => 'Transferencia Inmediata'
        ];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $arrOfPaymentIDs = $this->filterPaymentIds($accountability); 
            $paymentFrequency = array_count_values($arrOfPaymentIDs);
            
            $list_payment_intentions_accountability = [];
            foreach ($accountability as $key => $value) {
                
                $payment_date = (new \DateTime(substr($value, 0, 8)))->format('Y-m-d');
                $accreditation_date = (new \DateTime(substr($value, 8, 8)))->format('Y-m-d');
                $total_amount = (double) (substr($value, 24, 9) .'.'. substr($value, 33, 2));
                $customer_id = ltrim(substr($value, 35, 8), '0');
                $customer = Yii::$app->db->createCommand('SELECT cu.code FROM customer cu WHERE cu.customer_id = :customer_id')
                    ->bindValue('customer_id', $customer_id)
                    ->queryOne();
                $payment_method = substr($value, 44, 4);
                $siro_payment_intention_id = preg_replace('/'.$customer['code'].'/', '', ltrim(substr($value, 103, 20), '0'),1);
                $collection_channel= substr($value, 123, 3);
                $rejection_code = substr($value, 126, 3);

                if($total_amount > 0){
                    $hitCount = intval($paymentFrequency[$siro_payment_intention_id]);
                    
                    $siro_payment_intention = Yii::$app->db->createCommand('SELECT spi.estado, spi.payment_id 
                        FROM siro_payment_intention spi 
                        WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();

                    $payment_intention_accountability = Yii::$app->db->createCommand('SELECT pia.payment_intention_accountability_id 
                        FROM payment_intentions_accountability pia 
                        WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();


                    $AccInstancesCounter = Yii::$app->db->createCommand('SELECT count(*) as counter 
                        FROM payment_intentions_accountability pia 
                        WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();
                    

                    if (isset($siro_payment_intention)) {
                        $totalAmountToCreate = ($siro_payment_intention['estado'] == "PROCESADA") ? ($hitCount-1) : $hitCount;

                        if (($siro_payment_intention['estado'] == "PROCESADA") && 
                                ($hitCount > 1) &&
                                ($AccInstancesCounter['counter'] < $totalAmountToCreate)) {
                            
                            $list_payment_intentions_accountability[$siro_payment_intention_id][] = $value;
                 
                        }
                    }
                }
            }
            
            foreach ($list_payment_intentions_accountability as $key => $payment_intentions_accountability) {
                $skipFirst = 0; 
                $cantDuplicados = count($payment_intentions_accountability); 
                if($cantDuplicados > 1){ 
                    foreach ($payment_intentions_accountability as $value) {
                        $payment_date = (new \DateTime(substr($value, 0, 8)))->format('Y-m-d');
                        $accreditation_date = (new \DateTime(substr($value, 8, 8)))->format('Y-m-d');
                        $total_amount = (double) (substr($value, 24, 9) .'.'. substr($value, 33, 2));
                        $customer_id = ltrim(substr($value, 35, 8), '0');
                        $customer = Yii::$app->db->createCommand('SELECT cu.code FROM customer cu WHERE cu.customer_id = :customer_id')
                         ->bindValue('customer_id', $customer_id)
                         ->queryOne();
                        $payment_method = substr($value, 44, 4);
                        $siro_payment_intention_id = preg_replace('/'.$customer['code'].'/', '', ltrim(substr($value, 103, 20), '0'),1);
                        $collection_channel= substr($value, 123, 3);
                        $rejection_code = substr($value, 126, 3);

                        $siro_payment_intention = Yii::$app->db->createCommand('SELECT spi.estado, spi.payment_id 
                                FROM siro_payment_intention spi 
                                WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
                                    ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                                    ->queryOne();

                        $isProcessed = ($siro_payment_intention['estado'] == "PROCESADA");
                        if($isProcessed && ($skipFirst == 0)){
                            // yes => skip the first one. *cause "PROCESADA" means the first payment of the intention was created and OK
                            $skipFirst++;
                        }else{
                            // create accountability instances for the payments found
                            $model = new PaymentIntentionAccountability();
                            $model->payment_date = $payment_date;
                            $model->accreditation_date =  $accreditation_date;
                            $model->total_amount =  $total_amount;
                            $model->customer_id = $customer_id;
                            $model->payment_method = $payment_method;
                            $model->siro_payment_intention_id = $siro_payment_intention_id;
                            $model->collection_channel_description = isset($codes_collection_channel[$collection_channel]) ? $codes_collection_channel[$collection_channel] : 'No se reconoce el código: ' . $collection_channel;
                            $model->collection_channel = $collection_channel;
                            $model->rejection_code = $rejection_code;
                            $model->created_at = date('Y-m-d');
                            $model->updated_at = date('Y-m-d');
                            $model->status = 'draft';
                            $model->is_duplicate = 1; // cause this creation process was if ($cantDuplicados > 1)
                            $model->save();
                        }
                    }
                }else{
                    unset($list_payment_intentions_accountability[$key]);
                }
            }
            $transaction->commit();

        } catch (\Exception $e) {
            $errorMsg = "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n";
            $this->stdout($errorMsg);
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_cron.txt'),
            $errorMsg,
            FILE_APPEND);
            $transaction->rollBack();

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch (ROLLBACK DONE): find-duplicate-payments ****', $e);
        }
    }

	/**
     * returns an array of payment IDs that then can be used to know how many times each repeats
     */
    private function filterPaymentIds($renditionArray){
        $arrOfPaymentIDs=[];
        foreach ($renditionArray as $payment) {
            $total_amount = (double) (substr($payment, 24, 9) .'.'. substr($payment, 33, 2));
            $customer_id = ltrim(substr($payment, 35, 8), '0');
            $customer = Yii::$app->db->createCommand('SELECT cu.code FROM customer cu WHERE cu.customer_id = :customer_id')
                ->bindValue('customer_id', $customer_id)
                ->queryOne();
            $siro_payment_intention_id = preg_replace('/' . $customer['code'] . '/', '', ltrim(substr($payment, 103, 20), '0'), 1);
            // only return if the payment is a valid amount
            if ($total_amount > 0) array_push($arrOfPaymentIDs,$siro_payment_intention_id);
        }
        return $arrOfPaymentIDs;
    }

    //todo : update cronjob in production to trigger this action: siro/close-payment-intention
    /**
     * this function is intended to close all payment intentions that go over the limit lifespan of about 10-15 minutes
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

    // todo 1: merge previous actions // still to do// also check bpr and all other rejection channels
    // todo 2: make log file in production with correct permissions to write and read
    // todo 3: update crontab to trigger this action at the same frequency of the previous actions
    // todo 4: dont forget to add the $save variable for debugging needs
    /**
     * NEW
     * this is a merge from previous action to check payments and duplicates,
     * in a hope for it to be more efficient and readable.
     * 
     * This action is triggered by a system task (cronjob) and logs to /var/log/siro-revise-payments.log
     */
    public function actionPaymentsRevisor($save = false)
    {
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
        $this->stdout("\n----SIRO SINGLE PAYMENTS REVISOR INITIATED (SINGLE + DUPLICATE CHECKER)---- ".date("Y-m-d H:i:s")."\n");
        //todo: create a model that stores company capability to run this, store its ids  like 2,7 in the westnet case, and the API consumption numbers too
    	$companies = Company::find()->where(['in', 'company_id', [2,7]])->all();
    	foreach ($companies as $company) {
    		$this->reviseAllPayments($company, $save);
    	}
        $this->stdout("\n----END---- ".date("Y-m-d H:i:s")."\n");
	     
    }

    /**
     * gets an array of payments an unsets all invalid payments
     * based on amount and rejection codes
     */
    private function unsetInvalidPayments($payments_arr){

        // iterate payments arr
        foreach($payments_arr as $_index => $payment_data){
            // check validity
            $is_valid_payment = PaymentIntentionAccountability::checkPaymentValidity($payment_data);

            // echo ("index: $_index Valid: ".(($is_valid_payment)?"TRUE":"FALSE")." "
            // ."($".$payment_data['total_amount'].")"
            // ."(R-code ".$payment_data['rejection_code'].")"
            
            // ."\n");
            
            // unset invalid payments
            if(!$is_valid_payment){
                unset($payments_arr[$_index]);
            }else{
                // echo ("index: $_index Valid: ".(($is_valid_payment)?"TRUE":"FALSE")." "
                // ."($".$payment_data['total_amount'].")"
                // ."(R-code ".$payment_data['rejection_code'].")"
                
                // ."\n");
            }
        }

        // return clear array
        return $payments_arr;
    }


    /**
     * 
     */
    private function reviseAllPayments($company, $save){
        $this->stdout("INFO\n");
        $this->stdout("Company: ".$company->name."\n");

        // get data from siro (or debug data locally)
        $accountability = PaymentIntentionAccountability::getSiroDataArray($debug_mode = true, $company->company_id, $company->tax_identification);

        // start process of payment revision 
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // process accountability array into a decoded ordered version
            $acc_decoded_arr = PaymentIntentionAccountability::processPaymentAccountabilityApi($accountability);

            /**
             * FILTER ALL PAYMENTS THAT ARE NOT VALID
             */
            if(Yii::$app instanceof Yii\console\Application) echo ("(".count($acc_decoded_arr).") before values\n");
            $acc_valid_payments = self::unsetInvalidPayments($acc_decoded_arr);
            if(Yii::$app instanceof Yii\console\Application) echo ("(".count($acc_valid_payments).") after values\n");

            $intentions_ids = [];
            foreach($acc_valid_payments as $_index => $payment_data){
                // if(empty($payment_data['siro_payment_intention_id'])) echo "$_index index has an empty payment intention?\n";
                $intentions_ids[] = $payment_data['siro_payment_intention_id'];
            }
            $intentions_freq = array_count_values($intentions_ids);

            /**
             * DEBUG
             */
            // var_dump($accountability[$_index]); // in case you want to know the original values the current $payment_data were taken from
            $created_payments_debug = [];
            $invalid_payments_debug = [];
            $duplicate_counter = 0;
            foreach($intentions_freq as $_index => $qty){
                if($qty>1){
                    $duplicate_counter++;
                    // echo "$_index-$qty\n";
                }
            }

            foreach ($acc_valid_payments as $_index => $payment_data) {
                // if(!($_index < 5)) continue; // skip for debugging purposes

                $payment_validity = PaymentIntentionAccountability::checkPaymentOccurrences($payment_data, $intentions_freq);
                // if(!empty($payment_validity['error_msg'])) echo 'Error msg: '.$payment_validity['error_msg']."\n";
                
                $payment_end_status = '';
                if($payment_validity['create_payment']){
                    $payment_end_status .= "payment is VALID - CREATING PAYMENT - index: $_index - siro_payment_id: ".$payment_data['siro_payment_intention_id']." - name: ".$payment_data['full_name']." - code: ".$payment_data['customer_code']."\n";
                    // create accountability record
                    $model = new PaymentIntentionAccountability();
                    $is_saved = $model->createPaymentAccountabilityRecord($payment_data);
                    if($is_saved){
                        $created_payments_debug[] = $payment_data;
                    }
                }else{
                    // $payment_end_status .= "payment does NOT need to be accounted for - SKIPPING - index: $_index - siro_payment_id: ".$payment_data['siro_payment_intention_id']."\n";
                    $invalid_payments_debug[] = $payment_data;
                }
                if(Yii::$app instanceof Yii\console\Application) echo $payment_end_status;
            }
            /**
             * DEBUG
             */            
            if(Yii::$app instanceof Yii\console\Application) {
                echo ('$created_payments_debug counter ('.count($created_payments_debug).")\n");
                echo ('$invalid_payments_debug counter ('.count($invalid_payments_debug).")\n");
                echo ('$acc_valid_payments count('.count($acc_valid_payments).")\n"); // should be equal to the sum of $intentions_freq + $duplicate_counter
                echo ('$intentions_freq count('.count($intentions_freq).")\n");
                echo ("DUPLICATES:($duplicate_counter)\n");
            }

            if($save){
                $transaction->commit();
            }else{
                $transaction->rollBack();
            }
            // echo "'--end--'\n";//debugging purpose
            // die();//debugging purpose

        } catch (\Exception $e) {
            $errorMsg = "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n";
            $this->stdout($errorMsg);
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_cron.txt'),
            $errorMsg,
            FILE_APPEND);
            $transaction->rollBack();

            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch (ROLLBACK DONE): siro/checker-of-payments ****', $e);
        }
            
    }


}