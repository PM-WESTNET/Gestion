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

            if(isset($request['cierre_masivo']))
                return $this->MassiveClosure();

             if(isset($request['buscar_pagos_duplicados']))
                return $this->BuscarPagosDuplicados($request['company_id'], $request['date_from'], $request['date_to']);


            $company = Company::find()->where(['company_id' => $request['company_id']])->one();
            $cuit_administrator = str_replace('-', '', $company->tax_identification);

            $token = $this->GetTokenApi($request['company_id']);
            $accountability = $this->ObtainPaymentAccountabilityApi($token, $request['date_from'], $request['date_to'], $cuit_administrator, $request['company_id']);

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
                if(empty($accountability)){
                    Yii::$app->session->setFlash("danger", "Ha ocurrido un error en el servidor de Roela.");
                    return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
                }
	
                foreach ($accountability as $value) {
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
                        $siro_payment_intention = Yii::$app->db->createCommand('SELECT spi.estado, spi.payment_id FROM siro_payment_intention spi WHERE spi.siro_payment_intention_id = :siro_payment_intention_id')
                        ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                        ->queryOne();

                        $payment_intention_accountability = Yii::$app->db->createCommand('SELECT pia.payment_intention_accountability_id FROM payment_intentions_accountability pia WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                        ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                        ->queryOne();

                        if(isset($siro_payment_intention) && $siro_payment_intention['estado'] != "PROCESADA" && empty($payment_intention_accountability)){

                            $model = new PaymentIntentionAccountability();
                            $model->payment_date = $payment_date;
                            $model->accreditation_date =  $accreditation_date;
                            $model->total_amount =  $total_amount;
                            $model->customer_id = $customer_id;
                            $model->payment_method = $payment_method;
                            $model->siro_payment_intention_id = $siro_payment_intention_id;
                            $model->collection_channel_description = $codes_collection_channel[$collection_channel];
                            $model->collection_channel = $collection_channel;
                            $model->rejection_code = $rejection_code;
                            $model->created_at = date('Y-m-d');
                            $model->updated_at = date('Y-m-d');
                            
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
                $transaction->commit();

            } catch (\Exception $e) {
                $transaction->rollBack();
            }
            
  
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
    public function BuscarPagosDuplicados($company_id, $date_from, $date_to){
        $company = Company::find()->where(['company_id' => $company_id])->one();
        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        // mini debug variables to save api response times being so slow...
        $accountability = []; // null definition for rendition data based on if the file already exists *debugging* or not.
        if($this->debug && file_exists($this->filePath)){
            // Debug enabled AND file EXISTS...
            $accountability = json_decode(fgets(fopen($this->filePath,'r')),true); // open. get data. json_decode the data as an Assoc Array.
        }else{
            // get a new file with real data from siro api..
            $token = $this->GetTokenApi($company_id);
            $accountability = $this->ObtainPaymentAccountabilityApi($token, $date_from, $date_to, $cuit_administrator, $company_id);
        }

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
            if(empty($accountability)){
                Yii::$app->session->setFlash("danger", "Ha ocurrido un error en el servidor de Roela.");
                return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
            }

            // todo: WIP refactoring for a more efficient approach
            $data_assoc_array = $this->filterAccData($accountability);
            $paymentFrequency = array_count_values($data_assoc_array);
            var_dump(count($accountability));
            var_dump("accountability",count($accountability),
            "data_assoc_array",count($data_assoc_array));
            var_dump($data_assoc_array);
            die('end');
            
            // $arrOfPaymentIDs = $this->filterPaymentIds($accountability); 
            // // array mapping of the times an item repeats on an array
            // //   84976 => int 1
            // //   84956 => int 2
            // // used for knowing how many times a siro_payment_intention_id is repeated (usually no more than 2 times *SIRO BUG)
            // $paymentFrequency = array_count_values($arrOfPaymentIDs);
            // // var_dump("paymentFrequency",$paymentFrequency);
            var_dump(substr(($accountability[5]), 103, 20));
            var_dump("accountability",$accountability);
            
            die('true');
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


                // filtering successful payments based on amount *more than 0
                if($total_amount > 0){
                    $hitCount = intval($paymentFrequency[$siro_payment_intention_id]); // the keys of this array are payment intention ids like ['84976']  
                    
                    //DEBUG: echo all the valid payments of a customer ID
                    if($this->debug && $customer_id == '89385'){ 
                        var_dump(
                            $payment_date,
                            $accreditation_date,
                            $total_amount,
                            $customer_id,
                            $siro_payment_intention_id,
                            $hitCount,
                            $key
                        );
                    }

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

                    // added this new query to count the number of payments done by the payment checker
                    // generally speaking, this only necessary in the extreme case of MORE THAN 2 errors of duplicated payments
                    $AccInstancesCounter = Yii::$app->db->createCommand('SELECT count(*) as counter 
                        FROM payment_intentions_accountability pia 
                        WHERE pia.siro_payment_intention_id = :siro_payment_intention_id')
                            ->bindValue('siro_payment_intention_id', $siro_payment_intention_id)
                            ->queryOne();
                    

                    if (isset($siro_payment_intention)) {
                        $totalAmountToCreate = ($siro_payment_intention['estado'] == "PROCESADA") ? ($hitCount-1) : $hitCount; // minus 1 IF the payment is already 'processed'
                        // case 1: single payment
                        if ($siro_payment_intention['estado'] != "PROCESADA" && 
                            empty($payment_intention_accountability)) {
                                
                            //$list_payment_intentions_accountability[$siro_payment_intention_id][] = $value;
                        }
                        // case 2: double or more payments with first intention with status PROCESADA
                        else if (($siro_payment_intention['estado'] == "PROCESADA") && // if its already processed
                                ($hitCount > 1) && // there are more than 1 duplicates
                                ($AccInstancesCounter['counter'] < $totalAmountToCreate)) {
                            
                            $list_payment_intentions_accountability[$siro_payment_intention_id][] = $value;
                            // structure for duplicates:
                            // 82740 => 
                            //     array (size=2)
                            //     0 => string '20220105...
                            //     1 => string '20220105...

                        }
                        // commented a possible third case of error:
                        // case 3: first payment WASNT processed correctly and still made duplicates after taking the money *SIRO BUG
                        else if (($siro_payment_intention['estado'] != "PROCESADA") && // if its already processed
                                ($hitCount > 1) && // there are more than 1 duplicates
                                ($AccInstancesCounter['counter'] < ($hitCount-1))) { 
                                
                            var_dump(
                                $payment_date,
                                $accreditation_date,
                                $total_amount,
                                $customer_id,
                                $siro_payment_intention_id,
                                $hitCount,
                                $key
                            );
                            $list_payment_intentions_accountability[$siro_payment_intention_id][] = $value;
                        }
                    }
                }
            }
            die('trace end');
            foreach ($list_payment_intentions_accountability as $key => $payment_intentions_accountability) {
                $skipFirst = 0; // starts at 0 and skips the first payment creation if the payment duplicate is already PROCESADA
                $cantDuplicados = count($payment_intentions_accountability); 
                if($cantDuplicados > 1){ // this is the key to filter duplicated payments
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
                            $model->collection_channel_description = $codes_collection_channel[$collection_channel];
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
            $transaction->rollBack();
            var_dump($e);
            die();
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

    /**
     * Return all data filtered from dataccountability retrieved from SiroBank
     * 
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

        return $filtered_data; // return filtered data objects
    }

    // private function createPaymentAccountability($valueStruct){
    //     $model = new PaymentIntentionAccountability();
    //     $model->payment_date = $payment_date;
    //     $model->accreditation_date =  $accreditation_date;
    //     $model->total_amount =  $total_amount;
    //     $model->customer_id = $customer_id;
    //     $model->payment_method = $payment_method;
    //     $model->siro_payment_intention_id = $siro_payment_intention_id;
    //     $model->collection_channel_description = $codes_collection_channel[$collection_channel];
    //     $model->collection_channel = $collection_channel;
    //     $model->rejection_code = $rejection_code;
    //     $model->created_at = date('Y-m-d');
    //     $model->updated_at = date('Y-m-d');
    //     $model->status = 'draft';
    //     return $model->save();
    // }

}
