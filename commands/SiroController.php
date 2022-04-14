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


class SiroController extends Controller{

	public function actionCheckerOfPayments()
    {
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
    	$companies = Company::find()->where(['in', 'company_id', [2,7]])->all();

    	foreach ($companies as $key => $company) {
    		$this->SearchCheckerOfPayments($company);
    	}
	     
    }

    public function actionFindDuplicatePayments(){
    	/**
    	 * Redes del Oeste ID : 2
    	 * Servicargas ID : 7
    	 */
    	$companies = Company::find()->where(['in', 'company_id', [2,7]])->all();

    	foreach ($companies as $key => $company) {
    		$this->BuscarPagosDuplicados($company);
    	}
    }

    private function SearchCheckerOfPayments($company){
    	//if(isset($request['buscar_pagos_duplicados']))
	      //  return $this->BuscarPagosDuplicados($request['company_id'], $request['date_from'], $request['date_to']);

        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $token = $this->GetTokenApi($company->company_id);

        $yesterday = date('Y-m-d', strtotime("-7 days"));
        $today = date('Y-m-d');

        $accountability = $this->ObtainPaymentAccountabilityApi($token, $yesterday, $today, $cuit_administrator, $company->company_id);

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
                        $model->collection_channel_description = isset($codes_collection_channel[$collection_channel]) ? $codes_collection_channel[$collection_channel] : 'No se reconoce el código: ' . $collection_channel;
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
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_cron.txt'),
            "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n",
            FILE_APPEND);
            $transaction->rollBack();
        }
            
    }

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

        return json_decode($respuesta,true);
    } 

    public function BuscarPagosDuplicados($company){
        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $token = $this->GetTokenApi($company->company_id);

        $yesterday = date('Y-m-d', strtotime("-7 days"));
        $today = date('Y-m-d');

        $accountability = $this->ObtainPaymentAccountabilityApi($token, $yesterday, $today, $cuit_administrator, $company->company_id);
        
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
            file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador_pagos_duplicados_cron.txt'),
            "Ha Ocurrido un error: \n" .
            "Hora: " . date('Y-m-d H:m:s') . "\n" .
            "Respuesta de Siro: " . json_encode($accountability) . "\n" .
            "Error: " . json_encode($e) .
            "-----------------------------------------------------------------------------\n",
            FILE_APPEND);
            $transaction->rollBack();
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
}