<?php

namespace app\modules\westnet\notifications\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\checkout\models\PaymentMethod;
use app\modules\westnet\notifications\models\search\PaymentIntentionAccountabilitySearch;
use yii\db\Query;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class SiroController extends Controller
{
    private $debug = true; // debug variable used to not spam the API from siro (*they have a limited amount of requests availible)
    private $filePath = __DIR__ . '/rendition.txt'; // the file name and path to use for debugging purposes. Delete it manually to get new info from Siro.
    private $codes_collection_channel = [
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
    
    
    /**
     * Return token access api
     */
    private function GetTokenApi($company_id){
        $company = Company::findOne(['company_id' => $company_id]);
        $username = Config::getConfigForCompanyID('siro_username_'.$company->fantasy_name,$company_id)['description'];
        $password = Config::getConfigForCompanyID('siro_password_'.$company->fantasy_name, $company_id)['description'];

        $url = Config::getConfig('siro_url_get_token');

        $conexion = curl_init();

        $datos = array(
            "Usuario" => $username,
            "Password" => $password
        );

        curl_setopt($conexion, CURLOPT_URL,$url->item->description);

        curl_setopt($conexion, CURLOPT_POSTFIELDS, json_encode($datos));

        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'POST'); 

        $respuesta=curl_exec($conexion);

        curl_close($conexion);

        return json_decode($respuesta,true);
    }

    /**
     * Search intention payment created in BD of Siro
     */
    private function ObtainPaymentAccountabilityApi($token,$date_from, $date_to, $cuit_administrator, $company_id){
        $url = 'https://apisiro.bancoroela.com.ar:49220/siro/Listados/Proceso';
        $authorization = "Authorization: Bearer ".$token['access_token'];

        $conexion = curl_init();

        $datos = array(
            'fecha_desde' => $date_from,
            'fecha_hasta' => $date_to,
            'cuit_administrador' => $cuit_administrator,
            'nro_empresa' => ($company_id == 2) ? 5150075933 : 5150076022, //Redes del Oeste ID de convenio = 5150075933 Servicargas ID de convenio = 5150076022
        );


        curl_setopt($conexion, CURLOPT_URL,$url);
        curl_setopt($conexion, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'POST'); 

        $respuesta=curl_exec($conexion);

        curl_close($conexion);

        // debugging purposes //
        // create a file and save it with the rendition data from date range.
        if($this->debug && !file_exists($this->filePath)){
            $file = fopen($this->filePath,'w'); // open file
            fwrite($file,$respuesta); // write to file
            fclose($file); // close file

            //(saved info to file without json decoding)
        }
        return json_decode($respuesta,true);
    } 

    /**
     * Checker of payments function works as the indexer of actions for the view of Contrastador de Pagos
     * It firstly decides what action to take based on the parameters from de Request: Masive Closure, Search for Duplicated Payments, etc..
     * By default, it searches for any failed attempts for payment. 
     * 
     * This means:
     * - Get Rendition for the accountability of all payments between a date range.
     * - Use some logic to determine which payments failed (based on amount and payment intention id).
     * - Push all cases into array and create all corresponding payments for all the errors into PaymentIntentionAccountability model.
     * - Masive closure is then used to manually create the payments from the previous step.
     */
    public function actionCheckerOfPayments()
    {
        $this->layout = '/fluid'; // sets no margin for this view
        if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();

            if(isset($request['cierre_masivo'])) return $this->MassiveClosure();

            if(isset($request['check_missing_payments']))
                return $this->CheckMissingPayments(
                        $request['company_id'],
                        $request['date_from'],
                        $request['date_to']
                        );  
        }

        $searchModel = New PaymentIntentionAccountabilitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $collectionChannelNamesArr = $searchModel->getArrColletionChannelDescriptions();
        $companyNamesArr = Company::getArrCompanyNames();
        $statusArr = $searchModel->getArrStatus();
        $paymentMethodArr = $searchModel->getArrPaymentMethod();

        if (!empty($dataProvider)) {
            return $this->render(
                    'index',
                    [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'companyNamesArr' => $companyNamesArr,
                        'collectionChannelNamesArr' => $collectionChannelNamesArr,
                        'statusArr' => $statusArr,
                        'paymentMethodArr' => $paymentMethodArr,
                    ]
                );
        }


        return $this->render('index');
    }

    public function actionCancel($id){
        $model = PaymentIntentionAccountability::find()->where(['payment_intention_accountability_id' => $id])->one();
        $model->status = 'cancelled';
        $model->save();

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
    }

    public function actionConfirm($id){
        $transaction = Yii::$app->db->beginTransaction();
        $model = PaymentIntentionAccountability::find()->where(['payment_intention_accountability_id' => $id])->one();
        $customer = Customer::findOne(['customer_id' => $model->customer_id]);
        $payment_method = PaymentMethod::findOne(['name' => 'Botón de Pago']);

        $payment = new Payment([
            'customer_id' => $customer->customer_id,
            'amount' => $model->total_amount,
            'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
            'company_id' => $customer->company_id,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'status' => 'closed'
        ]);

        if ($payment->save(false)) {
            $payment_item = new PaymentItem();
            $payment_item->amount = $payment->amount;
            $payment_item->description = 'Intención de Pago (Banco Roela) ' . $model->siro_payment_intention_id;
            $payment_item->payment_method_id = $payment_method->payment_method_id;
            $payment_item->payment_id = $payment->payment_id;
            $payment_item->paycheck_id = null;
            
            $customer->current_account_balance -= $model->total_amount;

            $model->payment_id = $payment->payment_id;
            $model->status = 'payed';
            $model->save();

            $payment_item->save(false);
            $customer->save(false);

            $transaction->commit();
            Yii::$app->session->setFlash("success", "Se ha creado el pago correctamente.");

        } else {
            $transaction->rollBack();
            Yii::$app->session->setFlash("danger", "No se ha podido crear el pago.");
        }

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
    }

    /**
     * Closes all the Payment Intentions found
     *
     */
    public function MassiveClosure(){
        $transaction = Yii::$app->db->beginTransaction();
        $models = PaymentIntentionAccountability::find()->where(['status' => 'draft', 'payment_id' => null])->all();
        $payment_method = PaymentMethod::findOne(['name' => 'Botón de Pago']);
        try {
            foreach ($models as $key => $model) {
                $customer = Customer::findOne(['customer_id' => $model->customer_id]);
                $payment = new Payment([
                    'customer_id' => $customer->customer_id,
                    'amount' => $model->total_amount,
                    'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
                    'company_id' => $customer->company_id,
                    'date' => (new \DateTime('now'))->format('Y-m-d'),
                    'status' => 'closed'
                ]);

                if ($payment->save(false)) {
                    $payment_item = new PaymentItem();
                    $payment_item->amount = $payment->amount;
                    $payment_item->description = 'Intención de Pago (Banco Roela) ' . $model->siro_payment_intention_id;
                    $payment_item->payment_method_id = $payment_method->payment_method_id;
                    $payment_item->payment_id = $payment->payment_id;
                    $payment_item->paycheck_id = null;
                    
                    $customer->current_account_balance -= $model->total_amount;

                    $model->payment_id = $payment->payment_id;
                    $model->status = 'payed';
                    $model->save();

                    $payment_item->save(false);
                    $customer->save(false);
                }
            }
            Yii::$app->session->setFlash("success", "Se han creado los pagos correctamente.");
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash("danger", "No se han podido crear los pagos.");
        }

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));  
    }

    /**
     * Searches for duplicated payment items based on logic between statuses and positive payment amounts
     */
    public function CheckMissingPayments($company_id, $date_from, $date_to){
        set_time_limit(0);
        //* DEBUG: variables to save api response times being so slow...
        $accountability = $this->getAccFromAPIorFile($company_id, $date_from, $date_to);
        // die(); // uncomment this if you want to download a new rendition.txt and not loop through anything

        // redirect to main view in case the rendition didnt came as expected
        if(empty($accountability) or isset($accountability['Message'])){
            Yii::$app->session->setFlash("danger", "Ha ocurrido un error en el servidor de Roela.");
            if(isset($accountability['Message'])){
                $msg = $accountability['Message'];
                Yii::$app->session->addFlash("danger", "$msg");
            }
            return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // transform data rendition lines into objects with the data of the payment
            $renditionObjects = $this->filterAccData($accountability);

            //* EXAMPLE: array mapping of the times an item repeats on an array
            //*   84976 => int 1 // one time
            //*   84956 => int 2 // two times

            // get an array of the payment intentions ID's
            // count how many times they repeat each
            $paymentFrequency = array_count_values($this->filterPaymentIdsV2($renditionObjects,1)); // 1:skip payments with amount less than zero.

            // $customer_id_DEBUG = '40298'; //* DEBUG

            $paymentsToCreate = [];
            foreach ($renditionObjects as $key => $paymentObj) {

                $payment_date = $paymentObj['payment_date'];
                $accreditation_date = $paymentObj['accreditation_date'];
                $total_amount = $paymentObj['total_amount'];
                $payment_method = $paymentObj['payment_method'];
                $collection_channel= $paymentObj['collection_channel'];
                $rejection_code = $paymentObj['rejection_code'];
                $siro_payment_intention_id = $paymentObj['siro_payment_intention_id'];
                $customer_id = $paymentObj['customer_id'];

                // filtering successful payments based on amount *more than 0
                // if($total_amount > 0 && $customer_id == $customer_id_DEBUG){ //* DEBUG to check a single customer's payments
                if($total_amount > 0){

                    // get how many times the current payment ID is repeated on the payments rendition (to check for duplicates)
                    $hitCount = intval($paymentFrequency[$siro_payment_intention_id]); 
                    
                    // check that the payment intention id exists in our database
                    $siro_payment_intention = Yii::$app->db->createCommand('SELECT spi.estado, spi.payment_id 
                        FROM siro_payment_intention spi 
                        WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();

                    // check that the payment isnt already created in the checkers registries
                    //* added count for the number of payments done by the payment checker. this only necessary in the extreme case of MORE THAN 2 errors of duplicated payments
                    $payment_intention_accountability = Yii::$app->db->createCommand(
                        'SELECT pia.payment_intention_accountability_id, 
                                count(*) as counter
                        FROM payment_intentions_accountability pia 
                        WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();
                    
                    //* DEBUG: echo all the valid payments of a customer ID
                    // if($this->debug && $customer_id == $customer_id_DEBUG){
                    //     var_dump('//DEBUG//');
                    //     var_dump(
                    //         $paymentObj,
                    //         $key,
                    //         $siro_payment_intention,
                    //         $payment_intention_accountability,
                    //         $hitCount
                    //     );
                    // }

                    // if the intention exists on our database
                    if (isset($siro_payment_intention) && $siro_payment_intention) {

                        $wasProcessed = ($siro_payment_intention['estado'] == "PROCESADA");
                        $isDuplicate = ($hitCount > 1);
                        $totalAmountToCreate = $wasProcessed ? ($hitCount-1) : $hitCount; // minus 1 IF the payment is already 'processed'
                        $pushPaymentToCreate = false;   // boolean to check if the current payment should be created. resets to false every iteration.
                        
                        // if the intention isnt already contrasted
                        if(isset($payment_intention_accountability) && empty($payment_intention_accountability)){


                            // divide the cases in two: either they are already processed OR they dont.
                            if(!$wasProcessed){
                                // A possible third case of error:
                                // case 3: first payment WASNT processed correctly and still made duplicates after taking the money *SIRO BUG?
                                // is duplicated AND the current amount of payments checked are less than the total that should be created
                                if ($isDuplicate && ($payment_intention_accountability['counter'] < $totalAmountToCreate)) { 
                                    $pushPaymentToCreate = true;
                                    // echo " case 3";
                                }
                                // case 1: single payment   *this is the most common case.
                                else{
                                    $pushPaymentToCreate = true;
                                    // echo " case 1";
                                }
                            }else{
                                // case 2: double or more payments with first intention with status PROCESADA
                                // is duplicated AND the current amount of payments checked are less than the total that should be created
                                if($isDuplicate && ($payment_intention_accountability['counter'] < $totalAmountToCreate)){
                                    $pushPaymentToCreate = true;
                                    // echo " case 2";
                                }else{
                                    //* if the payment IS processed correctly and is SINGLE then its the default case for a correctly processed payment and should be ignored.
                                    // echo " case no worries </br>";
                                }
                            }
                        }

                        // push into array to create and reflect payment
                        if($pushPaymentToCreate) {
                            $paymentObj['isDuplicate'] = $isDuplicate;
                            $paymentObj['wasProcessed'] = $wasProcessed;
                            $paymentsToCreate[$siro_payment_intention_id][]=$paymentObj;

                            // var_dump($siro_payment_intention_id,isset($siro_payment_intention));
                            // var_dump("pushed $total_amount customer $customer_id on key $key");
                        }
                    }
                }else{
                    //* if the payment has amount 0 goes to here and does nothing!..
                }
            }
            // var_dump($paymentsToCreate);
            foreach ($paymentsToCreate as $currentPayIntID => $paymentsArr){
                $skipFirst = 0; // starts at 0 and skips the first payment creation if the payment duplicate is already PROCESADA

                foreach ($paymentsArr as $paymentObj){
                    if(($paymentObj['wasProcessed']) && ($skipFirst == 0)){
                        // yes => skip the first one. *cause "PROCESADA" means the first payment of the intention was created and OK
                        $skipFirst++;
                    }else{
                        // create accountability instances for the payments found -1 if the payment is processed.
                        $response = $this->createPaymentAccountability($paymentObj);
                        if(!$response){
                            // var_dump("payment obj got an error. $currentPayIntID");
        
                        }else{
                            // var_dump("payment created for obj: $currentPayIntID");
                        }
                    }
                }
            }
            // die();
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $e;
        }
        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
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
    private function getAccFromAPIorFile($company_id, $date_from, $date_to){
        $company = Company::find()->where(['company_id' => $company_id])->one();
        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $accountability = []; // null definition for rendition data based on if the file already exists *debugging* or not.
        if($this->debug && file_exists($this->filePath)){
            // Debug enabled AND file EXISTS...
            $accountability = json_decode(fgets(fopen($this->filePath,'r')),true); // open. get data. json_decode the data as an Assoc Array.
        }else{
            // get a new file with real data from siro api..
            $token = $this->GetTokenApi($company_id);
            $accountability = $this->ObtainPaymentAccountabilityApi($token, $date_from, $date_to, $cuit_administrator, $company_id);
        }
        return $accountability;
    }
    /**
     * Return all data filtered from dataccountability retrieved from the online bank rendition
     */
    private function filterAccData($rendition_data){
        $filtered_data = array(); // return array of objects based on every value of the rendition data

        $cus_ids_array = array(); // arr of IDs of Customers. Same length as the rendition data array

        // generate a list of ids
        foreach($rendition_data as $key => $value){
            $customer_id = ltrim(substr($value, 35, 8), '0');
            array_push($cus_ids_array,$customer_id);
        }
        //array to string conversion *comma separated
        $ids_string = implode(',',$cus_ids_array);

        $customerQuery = (new Query)
            ->select(['cu.code','cu.customer_id','cu.name'])
            ->from('customer cu')
            ->where(new \yii\db\Expression('FIND_IN_SET(customer_id, :cus_ids)'))
            ->addParams([':cus_ids' => $ids_string])
            ;

        $customer = $customerQuery->all();
        $customer = array_column($customer,'code','customer_id'); // $key:customer_id, $value:code


            // note: this two arrays have the same KEYS
            // var_dump($cus_ids_array[$key]); 
            // var_dump($rendition_data[$key]); 

        foreach($rendition_data as $key => $value){
            // set every variable of the object
            $payment_date = (new \DateTime(substr($value, 0, 8)))->format('Y-m-d');
            $accreditation_date = (new \DateTime(substr($value, 8, 8)))->format('Y-m-d');
            $total_amount = (double) (substr($value, 24, 9) .'.'. substr($value, 33, 2));
            $payment_method = substr($value, 44, 4);
            $collection_channel= substr($value, 123, 3);
            $rejection_code = substr($value, 126, 3);

            // both rendition_data and cus_ids_array have the same length, so we use the $key instead of trimming it again.
            $customer_id = $cus_ids_array[$key]; 

            // get the code from the previous query, using the array customer ID as an array KEY 
            $customer_code = $customer[$customer_id]; //* this fixes the issue that the previous code was querying the database all the time, slowing the API down

            // calculate code of the object based on indexes of the previous arrays
            $pattern = '/'.$customer_code.'/'; // replace with the customer code
            $string = ltrim(substr($value, 103, 20), '0'); // remove zeros from the string that should have the Siro Payment Intention ID and Code of the customer
            $siro_payment_intention_id = preg_replace($pattern, '', $string,1); // trim out the customer code and get only the payment intent ID

            // create object to be pushed
            $dataFromValue =  array(
                'payment_date' => $payment_date,
                'accreditation_date' => $accreditation_date,
                'total_amount' => $total_amount,
                'payment_method' => $payment_method,
                'collection_channel' => $collection_channel,
                'rejection_code' => $rejection_code,
                'siro_payment_intention_id' => $siro_payment_intention_id,
                'customer_id' => $customer_id,
                'customer_code' => $customer_code
            );
            // push into filtered data array of objects
            // array_push($filtered_data,)
            array_push($filtered_data,$dataFromValue);
        }
        // return filtered data objects from rendition
        return $filtered_data; 
    }

    /**
     * returns an array of payment IDs that then can be used to know how many times each repeats
     * V2: includes the capability to work with the newer data structure of an array of objects instead of the rendition trimming itself.
     * Optionally, skips payment values less than, or equal to, zero.
     */
    private function filterPaymentIdsV2($dataArr,$skipAmountZero = false){
        $arrOfPaymentIDs=[];
        foreach ($dataArr as $paymentObj) {
            // get siro_payment_intention_id of the object
            $siro_payment_intention_id = $paymentObj['siro_payment_intention_id'];

            // check if zero amount should be skipped
            if($skipAmountZero){
                // because of practical reasons, we only push the amounts that are valid (more than 0)
                $total_amount = $paymentObj['total_amount'];
                if ($total_amount > 0) array_push($arrOfPaymentIDs,$siro_payment_intention_id);
            }else{
                array_push($arrOfPaymentIDs,$siro_payment_intention_id);
            }
        }
        return $arrOfPaymentIDs;
    }

    private function createPaymentAccountability($paymentObj){

        $model = new PaymentIntentionAccountability();
        $model->payment_date = $paymentObj['payment_date'];
        $model->accreditation_date =  $paymentObj['accreditation_date'];
        $model->total_amount =  $paymentObj['total_amount'];
        $model->customer_id = $paymentObj['customer_id'];
        $model->payment_method = $paymentObj['payment_method'];
        $model->siro_payment_intention_id = $paymentObj['siro_payment_intention_id'];
        $model->collection_channel_description = $this->codes_collection_channel[$paymentObj['collection_channel']];
        $model->collection_channel = $paymentObj['collection_channel'];
        $model->rejection_code = $paymentObj['rejection_code'];
        $model->is_duplicate = $paymentObj['isDuplicate']; // added this condition to then see if it was a case 2 or 3.
        $model->created_at = date('Y-m-d');
        $model->updated_at = date('Y-m-d');
        $model->status = 'draft';
        
        return ($model->save());
    }

}
