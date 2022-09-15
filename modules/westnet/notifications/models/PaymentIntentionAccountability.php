<?php

namespace app\modules\westnet\notifications\models;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Company;
use app\modules\westnet\notifications\components\siro\ApiSiro;

use Yii;

/**
 * This is the model class for table "payment_intentions_Accountability".
 *
 * @property int $payment_intention_accountability_id
 * @property int $customer_id
 * @property int $siro_payment_intention_id
 * @property double $total_amount
 * @property int $payment_method
 * @property int $status
 * @property string $collection_channel
 * @property string $rejection_code
 * @property date $payment_date
 * @property date $accreditation_date
 * @property date $created_at
 * @property date $updated_at
 * @property int $payment_id
 *
 */
class PaymentIntentionAccountability extends ActiveRecord
{
    public $customer_name;
    public $company_name;

    //*note: i dont know why the previous programmer wanted to have them inside an array but i 
    //Collection Channels
    const CODES_COLLECTION_CHANNEL = [
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
        'BOC' => 'Botón Otros Crédito', // this was missing from siros pdf documentation
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
        'TI' => 'Transferencia Inmediata', // this was missing from our programmers data
        'SNP' => 'Transferencia Inmediata', // this was missing from siros pdf documentation
        'LNK' => 'Transferencia Inmediata', // this was missing from siros pdf documentation
        'IBK' => 'Transferencia Inmediata' // this was missing from siros pdf documentation
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_intentions_accountability';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['siro_payment_intention_id','customer_id'], 'number'],
            [['collection_channel_description','customer_name','company_name','payment_intention_accountability_id', 'customer_id', 'siro_payment_intention_id', 'total_amount', 'payment_method', 'status', 'collection_channel', 'rejection_code', 'payment_date', 'accreditation_date', 'created_at', 'updated_at', 'payment_id'], 'safe'],
        ];
    }

    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_intention_accountability_id' => 'ID',
            'customer_id' => 'ID Cliente',
            'siro_payment_intention_id' => 'ID Intención de Pago',
            'total_amount' => 'Monto Total',
            'payment_method' => 'Método de Pago',
            'status' => 'Estado',
            'collection_channel' => 'Canal de Cobro',
            'rejection_code' => 'Código de Rechazo',
            'payment_date' => 'Fecha de Pago',
            'accreditation_date' => 'Fecha de Acreditación',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
            'payment_id' => 'ID Pago',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }
    /**
     * Gives back an array of distinct values of CollectionChannelDescription from the models table
     */
    public function getArrColletionChannelDescriptions(){
        $arr = $this->find()->select('collection_channel_description')->distinct()->asArray()->indexBy('collection_channel_description')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }

    /**
     * Gives back an array of distinct values of Status from this models table
     */
    public function getArrStatus(){
        $arr = $this->find()->select('status')->distinct()->asArray()->indexBy('status')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }

    public function getArrPaymentMethod(){
        $arr = $this->find()->select('payment_method')->distinct()->asArray()->indexBy('payment_method')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }

    /**
     * before save trigger
     * in this case i use it to add dates without using behaviours
     * @return Boolean
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            // update if new 
            if ($this->isNewRecord) {
                $this->created_at = date("Y-m-d H:i:s");
            }
            $this->updated_at = date("Y-m-d H:i:s");
        }

        return parent::beforeSave($insert);
    }
    /**
     * returns the next value (used sequentially) needed in the context of siro's REGISTER DESIGN.
     * see: https://drive.google.com/file/d/1aJKatSu_BX78DTl9lsfOdbZLWb-hWzf6/view
     * @return String
     */
    private static function trimAndUpdate(&$reg_str, $length){
        // starts on 0 to length
        $value = substr($reg_str, 0, $length);
        // trims the pointed register clone
        $reg_str = substr($reg_str, $length);
        return $value;
    }


    /**
     * processes the a register line retrieved 
     * from siro API of payments for accountability.
     * @return Array
     */
    public static function decodePaymentRegisterLine($reg_str){
        $reg_decoded = [];

        // original register line
        $reg_decoded['original_register'] = $reg_str;

        // date on which the client made the payment
        $reg_decoded['payment_date'] = self::trimAndUpdate($reg_str, 8); 

        // date of accreditation of the payment in Banco Roela current account
        $reg_decoded['accreditation_date'] = self::trimAndUpdate($reg_str, 8);

        // comes from the First Expiration of the barcode
        $reg_decoded['first_expiration'] = self::trimAndUpdate($reg_str, 8);

        // payed amount. the decimal part are the two last digits. ex. "1.490,80" <- "00000149080". cast with (double)
        $reg_decoded['total_amount'] = self::trimAndUpdate($reg_str, 11); 

        // user identification from which the payment was originated. check doc for more info
        $reg_decoded['customer_id'] = self::trimAndUpdate($reg_str, 8);

        // the number code for the payment method. check doc for more info
        $reg_decoded['payment_method'] = self::trimAndUpdate($reg_str, 1);

        // the complete barcode from which the current payment originated . check doc for more info 
        $reg_decoded['bar_code'] = self::trimAndUpdate($reg_str, 59);

        // payment intention identificator used for Link de pagos, Pago mis Cuentas, Debito Directo, and such
        $reg_decoded['siro_payment_intention_id'] = self::trimAndUpdate($reg_str, 20);

        // payment channel from which the payment was made. check doc for more info . 
        $reg_decoded['collection_channel'] = self::trimAndUpdate($reg_str, 3);

        // rejection code for Debito Directo and Boton de Pagos. 
        //* note: for Boton de Pagos the rejection code returned is '402' 
        $reg_decoded['rejection_code'] = self::trimAndUpdate($reg_str, 3);

        // rejection description. check doc for more info
        $reg_decoded['rejection_description'] = self::trimAndUpdate($reg_str, 20);

        // amount of monthly payments for the payment. 
        //* note: zeros (0) padding added to the left side. is filled with empty spaces if no credit card was used
        $reg_decoded['payment_quotas'] = self::trimAndUpdate($reg_str, 2);

        // name of the credit card used for the payment 
        //* note: is filled with empty spaces if no credit card was used
        $reg_decoded['card'] = self::trimAndUpdate($reg_str, 15);

        // input information in the Filler3 field from the PMC file 
        $reg_decoded['filler'] = self::trimAndUpdate($reg_str, 60);

        // remote payment identificator. 
        //* note: unique for each payment. zeros (0) padding added to the left side
        $reg_decoded['payment_id'] = self::trimAndUpdate($reg_str, 10);

        // identificator for results of payment intentions made via API.
        //* note: is filled with empty spaces if the API was not used 
        $reg_decoded['result_id'] = self::trimAndUpdate($reg_str, 36);

        return $reg_decoded;
    }

    /**
     * formats all payment data for database and actually makes it somewhat more readable
     * receives the output from processPaymentRegisterLine() function.
     * @return Array
     */
    public static function formatPaymentData($payment_data){

        // tidy up the empty values
        foreach($payment_data as $key => $data_value){
            // only correct the empty spaces. let the boolean values be
            if( (ctype_space($data_value) or empty($data_value)) and !is_bool($data_value) ){
                $payment_data[$key] = "";
            }
        }

        // format dates
        $payment_data['payment_date'] = self::formatDate($payment_data['payment_date']);
        $payment_data['accreditation_date'] = self::formatDate($payment_data['accreditation_date']);
        $payment_data['first_expiration'] = self::formatDate($payment_data['first_expiration']);

        // transform total_amount into a float type
        $integer_part = 9; $decimal_part = 2;
        $payment_data['total_amount'] = (double)(substr($payment_data['total_amount'], 0, $integer_part) .'.'. substr($payment_data['total_amount'], $integer_part, $decimal_part));
        
        // trim excess zeros out of customer_id
        $character_to_trim = '0';
        $payment_data['customer_id'] = ltrim($payment_data['customer_id'], $character_to_trim);


        $customer = Yii::$app->db->createCommand('SELECT cu.code,cu.name,cu.lastname FROM customer cu WHERE cu.customer_id = :customer_id')
            ->bindValue('customer_id', $payment_data['customer_id']);

        // query one and save customer code into payment data
        $payment_data['customer_code'] = $customer->queryOne()['code'];
        $payment_data['full_name'] = $customer->queryOne()['name']." ".$customer->queryOne()['lastname'];

        // trimm siro payment intention the way the previous programmer was doing it. im not sure this is right.
        $trimmed_siro_payment_intention = ltrim($payment_data['siro_payment_intention_id'], $character_to_trim);
        $payment_data['siro_payment_intention_id'] = preg_replace('/'.$payment_data['customer_code'].'/', '', $trimmed_siro_payment_intention, 1);

        // trimm bar_code 
        $payment_data['decoded_bar_code'] = self::getTrimmedBarcode($payment_data['bar_code']);


        return $payment_data;
    }

    /**
     * receives an unformatted date and formats it.
     * by default its 'Y-m-d'
     * @return DateTime
     */
    private static function formatDate($unformatted, $format = 'Y-m-d'){
        return (new \DateTime($unformatted))->format($format);
    }

    /**
     * processes all the payments registers retrieved from Siro API call.
     * @return Array
     */
    public static function processPaymentAccountabilityApi($accountability){
        $processed_acc_file = [];
        foreach($accountability as $reg_value){
            // define empty array for intention data separation
            $reg_decoded = self::decodePaymentRegisterLine($reg_value);

            // edit and adapt data for it to be much more human-readable and have correct formatting for db.
            $payment_data = self::formatPaymentData($reg_decoded);

            // push data array into the new accountability array
            array_push($processed_acc_file, $payment_data);
        }
        return $processed_acc_file;
    }

    /**
     * Checks the ocurrences of payments id inside the tables related
     * siro payment intention and payment intentions accountability 
     * Compares ocurrences with current count frequency of payment and
     * decides whether or not if it should be created
     * 
     * @return Boolean
     */
    public static function checkPaymentOccurrences($payment_data, &$intentions_freq){
        // default value is FALSE. later changes if payment IS actually valid.
        // response is just the error string for debugging
        $response = [
            'create_payment' => false,
            'error_msg' => ''
        ];
        //todo : implement error_msg responses .. like.. someday or smt

        // searches for a siro_payment_intention that are already PROCESSED (0...1)
        $siro_payment_intention_query = Yii::$app->db->createCommand(
                'SELECT count(*) as qty, spi.estado 
                FROM siro_payment_intention spi 
                WHERE spi.siro_payment_intention_id = :siro_payment_intention_id
                AND spi.estado = :estado')
            ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id']) // ID as search key
            ->bindValue('estado', SiroPaymentIntention::STATUS_PROCESSED); // filter search only for correctly processed intentions
        // get counter
        $intentions_counter = (int)$siro_payment_intention_query->queryScalar();

        // searches for siro_payment_intention(s) that are already ACCOUNTED FOR (0...n)
        $accountability_query = Yii::$app->db->createCommand(
                'SELECT count(*) as qty 
                FROM payment_intentions_accountability pia 
                WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
            ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id']);
        // get counter
        $accountability_counter = (int)$accountability_query->queryScalar();


        // debug
        // var_dump($siro_payment_intention_query->rawSql); // debug
        // var_dump($accountability_query->rawSql); // debug
        // echo ($payment_data['siro_payment_intention_id']." - ($".$payment_data['total_amount'].")"." - "); 
        // echo ("Found ($intentions_counter) PROCESSED in siro_payment_intention - ");
        // echo ("Found ($accountability_counter) in payment_intentions_accountability");
        // echo "\n";

        $steps = '';
        $steps .= "STEPS TAKEN - ";
        //* note: counters can and should be only positive and zero integer values.
        // if not processed on intentions table
        if( $intentions_counter == 0 ){
            $steps .= "step 1 - ";
            if( $accountability_counter == 0 ){
                $steps .= "step 3 - ";
                // both payment isnt already processed and accountability records were not found
                $response['create_payment'] = true;
            }else{
                $steps .= "step 4 - ";
                // more than 1 register is found
                // create only if accountability_counter is less than CURRENT quantity of repetitions of siro_payment_intention_id's
                if( self::isDuplicate($intentions_freq, $payment_data) and 
                    self::hasDuplicatesStill($intentions_freq, $payment_data, $accountability_counter) 
                    ){
                    $steps .= "step 5 - ";
                    //$accountability_counter
                    $response['create_payment'] = true;
                    // remove 1 to the frequency counter. (the freq array's memory is being pointed with & )
                    //* the total amount of repeated VALID payments should be created later on when the transaction commits
                    $intentions_freq[$payment_data['siro_payment_intention_id']]--; 
                }
                else{
                    $steps .= "step 6 - ";
                    // either all accountability records were created in runtime OR were already created before the checker script ran 
                    // (for this specific payment intention ID)
                    // ...
                }

            }
        }else{
            $steps .= "step 2  - ";
            // payment is already created and processed for this register
            // ...

            // this step is added to compensate for the algorithm logic. As a Payment is ALREADY PAYED in the other siro_payment_intention table
            $accountability_counter++;

            // create only if accountability_counter is less than CURRENT quantity of repetitions of siro_payment_intention_id's
            if( self::isDuplicate($intentions_freq, $payment_data) and 
                self::hasDuplicatesStill($intentions_freq, $payment_data, $accountability_counter) 
                ){
                $steps .= "step 7 - ";
                //$accountability_counter
                $response['create_payment'] = true;
                // remove 1 to the frequency counter. (the freq array's memory is being pointed with & )
                //* the total amount of repeated VALID payments should be created later on when the transaction commits
                $intentions_freq[$payment_data['siro_payment_intention_id']]--; 
            }
        }
        $steps .= "END\n";
        // echo $steps; // debug

        // if($payment_data['siro_payment_intention_id'] == '244464'){
        //     echo $steps; // debug
        //     die();
        // }
        

        return $response;
    }

    private static function isDuplicate($intentions_freq, $payment_data){
        if( $intentions_freq[$payment_data['siro_payment_intention_id']] > 1 ) return true;
        return false;
    }

    private static function hasDuplicatesStill($intentions_freq, $payment_data, $accountability_counter){
        if( $intentions_freq[$payment_data['siro_payment_intention_id']] > $accountability_counter ) return true;
        return false;
    }
    /**
     * checks payment validity based on amount and rejection code
     */
    public static function checkPaymentValidity($payment_data){

        if( !($payment_data['total_amount'] > 0) ) return false;
        if( !empty($payment_data['rejection_code']) ) return false;
        //todo: ask if an empty payment intention id is a reason to reject a payment.
        // if( empty($payment_data['siro_payment_intention_id'])) return false;

        return true;
    }
    /**
     * this function receives a payment_data variable as an 
     * array of data from a pre-processed payment
     * Returns true if saved successfully
     * @return Boolean
     */
    public function createPaymentAccountabilityRecord($payment_data, $is_duplicate = false){

        // load data 
        $this->customer_id = $payment_data['customer_id'];
        $this->siro_payment_intention_id = $payment_data['siro_payment_intention_id'];
        $this->total_amount =  $payment_data['total_amount'];
        $this->payment_method = $payment_data['decoded_bar_code']['payment_method'];
        $collection = PaymentIntentionAccountability::CODES_COLLECTION_CHANNEL[$payment_data['collection_channel']];
        if(isset( $collection )){
            $this->collection_channel_description = $collection;
        }else{
            $this->collection_channel_description = 'No se reconoce el código: ' . $payment_data['collection_channel'];
        }
        $this->collection_channel = $payment_data['collection_channel'];
        $this->rejection_code = $payment_data['rejection_code'];
        $this->rejection_description = $payment_data['rejection_description'];

        $this->payment_date = $payment_data['payment_date'];
        $this->accreditation_date =  $payment_data['accreditation_date'];
        $this->first_expiration = $payment_data['first_expiration'];

        //later you can decode it if needed
        $this->bar_code = $payment_data['bar_code'];

        // we filter by this values so they should always be empty in the table , i guess...
        $this->rejection_code = $payment_data['rejection_code'];
        $this->bar_code = $payment_data['rejection_description'];

        $this->payment_quotas = $payment_data['payment_quotas'];
        $this->card = $payment_data['card'];
        $this->filler = $payment_data['filler'];
        $this->result_id = $payment_data['result_id'];

        $this->is_duplicate = ($is_duplicate) ? 1 : 0;


        // $siro_payment_intention = Yii::$app->db->createCommand('SELECT spi.estado, spi.payment_id FROM siro_payment_intention spi WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
        // ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
        // ->queryOne();
        if(empty($siro_payment_intention['payment_id'])){
            $this->status = 'draft';
            
        }else{
            $this->payment_id = $siro_payment_intention['payment_id'];
            $this->status = 'payed';
        }
        // var_dump('--- $model data displayed TO SAVE ---', $this); //debugging

        // save data to model
        $saved = $this->save();
        if(!$saved){
            echo "not saved correctly - ".""."\n";
            // var_dump($this->getErrorSummary(true));
        }

        // returns save TRUE only if the model saved successfully.
        return $saved;
    }

    /**
     * 
     */
    public static function getSiroDataArray($debug_mode = FALSE, $company_id, $company_tax_identification, $from_date, $to_date){
        // debug variables and data
        $testfile_name = 'testing-data-company-id-'.$company_id.'.txt';

        $test_data = null;
        if(file_exists($testfile_name) and $debug_mode){
            if(Yii::$app instanceof Yii\console\Application) echo ("Debug mode TRUE - Using file \"$testfile_name\"\n");
            $filecontents = file_get_contents($testfile_name);
            $test_data = (json_decode($filecontents));
        }

        $accountability = null;
        // choose between test_data (for debugging) or API call
        if(!is_null($test_data)){
            $accountability = $test_data;
        }else{
            // get token for API
            $token = ApiSiro::getTokenApi($company_id);
            
            // get cuit_administrator from company model
            $cuit_administrator = str_replace('-', '', $company_tax_identification);
            if(Yii::$app instanceof Yii\console\Application) echo ("Revise date range from: $from_date to: $to_date\n");
            if(Yii::$app instanceof Yii\console\Application) echo ("Cuit administrator selected: $cuit_administrator\n");

            $accountability = ApiSiro::ObtainPaymentAccountabilityApi($token, $from_date, $to_date, $cuit_administrator, $company_id);
            if($accountability == false) return false;
        }

        // re-encode data to save into a testing file in case we need it. (if file doesnt exist, create it)
        if(((!file_exists($testfile_name)) and $debug_mode)) file_put_contents($testfile_name, json_encode($accountability));
        
        // return data array of payment strings
        return $accountability;
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
    private static function getIntentionsFrequency($acc_valid_payments){
        $intentions_ids = [];
        foreach($acc_valid_payments as $_index => $payment_data){
            // if(empty($payment_data['siro_payment_intention_id'])) echo "$_index index has an empty payment intention?\n";
            $intentions_ids[] = $payment_data['siro_payment_intention_id'];
        }
        return array_count_values($intentions_ids);
    }

    /**
     * 
     */
    private static function getDuplicateCounter($intentions_freq){
        $duplicate_counter = 0;
        foreach($intentions_freq as $_index => $qty){
            if($qty>1){
                $duplicate_counter++;
                echo "$_index-$qty\n";
            }
        }        
        return $duplicate_counter;
    }

    /**
     * Process that checks all payments based on company_id given
     * checks individual missing payments from our system using Siros APIs
     * checks also for duplicated missing payments. 
     * 
     * recieves a Company model
     * 
     * 
     * @return Mixed why?
     */
    public static function revisePaymentsProcess($company, $from_date, $to_date){
        // fast check to know if company given is really enabled. (as this process can be triggered from any view or cron task)
        if(!in_array($company->company_id, SiroCompanyConfig::getEnabledCompaniesIds())) return false;

        // default return value. changes if something fails
        $ret_val = true;

        // get data from siro (or debug data locally)
        $accountability = PaymentIntentionAccountability::getSiroDataArray($debug_mode = false, $company->company_id, $company->tax_identification, $from_date, $to_date);
        if(empty($accountability)){
            $errorMsg = "ERROR: Accountability data array return value is empty, either from Siro endpoint or debug file.\n".
                        "Hora: " . date('Y-m-d H:m:s') . "\n";

            if(Yii::$app instanceof Yii\console\Application) echo $errorMsg;
        }

        // process accountability array into a decoded ordered version
        $acc_decoded_arr = PaymentIntentionAccountability::processPaymentAccountabilityApi($accountability);

        /**
         * FILTER ALL PAYMENTS THAT ARE NOT VALID
         */
        if(Yii::$app instanceof Yii\console\Application) echo ("(".count($acc_decoded_arr).") before values\n");
        $acc_valid_payments = self::unsetInvalidPayments($acc_decoded_arr);
        if(Yii::$app instanceof Yii\console\Application) echo ("(".count($acc_valid_payments).") after values\n");

        
        /**
         * DEBUG VARIABLES
         */
        // var_dump($accountability[$_index]); // in case you want to know the original values the current $payment_data were taken from
        $created_payments_debug = [];
        $failed_payments_debug = [];
        $invalid_payments_debug = [];
        $intentions_freq = self::getIntentionsFrequency($acc_valid_payments);
        $duplicate_counter = self::getDuplicateCounter($intentions_freq);

        foreach ($acc_valid_payments as $_index => $payment_data) {
            // if(!($_index < 5)) continue; // skip for debugging purposes

            // check if payment is duplicated based on frequency. this has to be done before the checkPaymentOccurrences function cause of pointers.
            $is_duplicate_payment = PaymentIntentionAccountability::isDuplicate($intentions_freq, $payment_data);
            $payment_validity = PaymentIntentionAccountability::checkPaymentOccurrences($payment_data, $intentions_freq);
            // if(!empty($payment_validity['error_msg'])) echo 'Error msg: '.$payment_validity['error_msg']."\n";
            
            //payment trace status message for debugging
            $payment_end_status = '';
            if($payment_validity['create_payment']){
                // create accountability record
                $model = new PaymentIntentionAccountability();
                $is_saved = $model->createPaymentAccountabilityRecord($payment_data, $is_duplicate_payment);
                if($is_saved){
                    $created_payments_debug[] = $payment_data;
                    $payment_end_status .= "Payment saved";
                }else{
                    //todo:  check what to do when a payment model fails to save correctly 
                    $failed_payments_debug[] = $payment_data;
                    $payment_end_status .= "Payment could not be saved";

                    // fail flag
                    $ret_val = false;
                }
                // concat more info for the valid payment cases
                $payment_end_status .= " - index: $_index";
                $payment_end_status .= " - siro_payment_id: ".$payment_data['siro_payment_intention_id'];
                $payment_end_status .= " - code: ".$payment_data['customer_code'];
                $payment_end_status .= " - name: ".$payment_data['full_name'];
                if($is_duplicate_payment) $payment_end_status .= " *DUPLICATED";
                $payment_end_status .= "\n";
            
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
            echo ('$failed_payments_debug counter ('.count($failed_payments_debug).")\n");
            echo ('$invalid_payments_debug counter ('.count($invalid_payments_debug).")\n");
            echo ('$acc_valid_payments count('.count($acc_valid_payments).") *total valid payments (duplicates included)\n"); // should be equal to the sum of $intentions_freq + $duplicate_counter
            echo ('$intentions_freq count('.count($intentions_freq).") *unique siro_payment_intention_id's\n");
            echo ("DUPLICATES:($duplicate_counter)\n");
            echo "--end--\n\n";//debugging purpose
        }

        // die();//debugging purpose
        
        // returns true if everything went ok
        return $ret_val;
    }


    /**
     * based on https://drive.google.com/file/d/1aJKatSu_BX78DTl9lsfOdbZLWb-hWzf6/view
     * this function returns a decoded array for the barcode information.
     * 
     * @return Array decoded array based on input string
     */
    public static function getTrimmedBarcode($reg_str){
        $trimmed_bc = [];

        $trimmed_bc['payment_method'] = self::trimAndUpdate($reg_str,4);
        $trimmed_bc['concept_id'] = self::trimAndUpdate($reg_str,1);
        $trimmed_bc['reference_number'] = self::trimAndUpdate($reg_str,8);
        $trimmed_bc['first_expiration_date'] = self::trimAndUpdate($reg_str,6);
        $trimmed_bc['first_expiration_amount'] = self::trimAndUpdate($reg_str,8); // 6 integers. 2 decimals
        $trimmed_bc['second_expiration'] = self::trimAndUpdate($reg_str,2);
        $trimmed_bc['second_expiration_amount'] = self::trimAndUpdate($reg_str,8);
        $trimmed_bc['accreditation_account_id'] = self::trimAndUpdate($reg_str,10);
        $trimmed_bc['check_digits'] = self::trimAndUpdate($reg_str,2);
        
        // return decoded arr
        return $trimmed_bc;
    }
}
