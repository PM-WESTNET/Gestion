<?php

namespace app\modules\westnet\notifications\models;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Company;

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
        return false;
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
        //todo: create db migration and make it into a model.
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

        //todo: change for active-record Customer.. or a better more performant way.
        $customer = Yii::$app->db->createCommand('SELECT cu.code FROM customer cu WHERE cu.customer_id = :customer_id')
            ->bindValue('customer_id', $payment_data['customer_id']);
        // query one and save customer code into payment data
        $payment_data['customer_code'] = $customer->queryOne()['code'];

        // trimm siro payment intention the way the previous programmer was doing it. im not sure this is right.
        $trimmed_siro_payment_intention = ltrim($payment_data['siro_payment_intention_id'], $character_to_trim);
        $payment_data['siro_payment_intention_id'] = preg_replace('/'.$payment_data['customer_code'].'/', '', $trimmed_siro_payment_intention, 1);

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
     * this function checks a payments validity based on its data
     * the payment must be decoded already into an array form,
     * provided by the formatPaymentData() function
     * 
     * @return Boolean
     */
    public static function checkPaymentValidity($payment_data){
        // default value is FALSE. later changes if payment IS actually valid.
        // response is just the error string for debugging
        $response = [
            'is_valid' => false,
            'error_msg' => ''
        ];

        // validity checker logic
        if($payment_data['total_amount'] > 0){
            // var_dump('payment_data',$payment_data);
            // echo "passes 1 $_index\n";//debugging purpose
            echo "payment id used: (".$payment_data['siro_payment_intention_id'].")\n";

            // searches for a siro_payment_intention that we COULD have related to the ones from the siro API (it could be that it doesnt exist for some reason)
            $siro_payment_intention = Yii::$app->db->createCommand(
                'SELECT spi.estado, spi.payment_id 
                FROM siro_payment_intention spi 
                WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
            ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id'])
            ->queryOne();

            // check payment intentions table
            if(!empty($siro_payment_intention)){

                // todo: modify this query to know how much payments to create in case of duplicates. do AFTER the check of Rejection channels
                // searches for an accountability record related to the current siro_payment_intention_id
                $payment_intention_accountability = Yii::$app->db->createCommand(
                    'SELECT pia.payment_intention_accountability_id 
                    FROM payment_intentions_accountability pia 
                    WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                ->bindValue('siro_payment_intention_id', $payment_data['siro_payment_intention_id'])
                ->queryOne();

                // check accountability table
                if(empty($payment_intention_accountability)){
                    // check status
                    if($siro_payment_intention['estado'] != SiroPaymentIntention::STATUS_PROCESSED){
                        $response['is_valid'] = true;
                        // $response['error_msg'] = "No errors";
                    }else{
                        $response['error_msg'] = "Our payment intention status is ".SiroPaymentIntention::STATUS_PROCESSED;
                    }
                }else{
                    $response['error_msg'] = "There is an accountability record related for this payment";
                }
            }else{
                $response['error_msg'] = "No siro payment intention related record was found";
            }

            // var_dump('siro_payment_intention and accountability',$siro_payment_intention, $payment_intention_accountability);//debugging purpose

            // if(
            //     !empty($siro_payment_intention) && // siro payment intention related record isnt empty
            //     $siro_payment_intention['estado'] != SiroPaymentIntention::STATUS_PROCESSED && // its status != "PROCESADA"
            //     empty($payment_intention_accountability)) // there is no accountability related record found for it
            // {
            //     // echo "passes 2 $_index\n";//debugging purpose
            // }
        }
        return $response;
    }

    /**
     * this function receives a payment_data variable as an 
     * array of data from a pre-processed payment
     * Returns true if saved successfully
     * @return Boolean
     */
    private function createPaymentAccountabilityRecord($payment_data, $save = true){

        // load data 
        $this->payment_date = $payment_data['payment_date'];
        $this->accreditation_date =  $payment_data['accreditation_date'];
        $this->total_amount =  $payment_data['total_amount'];
        $this->customer_id = $payment_data['customer_id'];
        $this->payment_method = $payment_data['payment_method'];
        $this->siro_payment_intention_id = $payment_data['siro_payment_intention_id'];

        $collection = PaymentIntentionAccountability::CODES_COLLECTION_CHANNEL[$payment_data['collection_channel']];
        // check if code exists on db 
        if(isset( $collection )){
            $this->collection_channel_description = $collection;
        }else{
            $this->collection_channel_description = 'No se reconoce el código: ' . $payment_data['collection_channel'];
        }

        $this->collection_channel = $payment_data['collection_channel'];
        $this->rejection_code = $payment_data['rejection_code'];

        
        if(empty($siro_payment_intention['payment_id'])){
            $this->status = 'draft';
            
        }else{
            $this->payment_id = $siro_payment_intention['payment_id'];
            $this->status = 'payed';
        }
        var_dump('--- $model data displayed TO SAVE ---', $this);
        // returns save TRUE only if the model saved successfully. if $save is false, returns that instead.
        return ($save) ? $this->save() : $save;
    }

}
