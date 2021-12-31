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
/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class SiroController extends Controller
{
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

    public function actionCheckerOfPayments()
    {
        $this->layout = '/fluid';
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

        $list_payment_intentions_accountability = PaymentIntentionAccountability::find()->all();
        if(!empty($list_payment_intentions_accountability)){
                $dataProvider = new ArrayDataProvider([
                    'allModels' => $list_payment_intentions_accountability,
                    'pagination' => [
                        'pageSize' => 15,
                    ],
                ]);
            return $this->render('index', ['dataProvider' => $dataProvider]);
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

    public function BuscarPagosDuplicados($company_id, $date_from, $date_to){
        $company = Company::find()->where(['company_id' => $company_id])->one();
        $cuit_administrator = str_replace('-', '', $company->tax_identification);

        $token = $this->GetTokenApi($company_id);
        $accountability = $this->ObtainPaymentAccountabilityApi($token, $date_from, $date_to, $cuit_administrator, $company_id);

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
            

            $list_payment_intentions_accountability = [];
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
                        $list_payment_intentions_accountability[$siro_payment_intention_id][] = $value;
                    }

                }

            }

            foreach ($list_payment_intentions_accountability as $key => $payment_intentions_accountability) {
                if(count($payment_intentions_accountability) > 1){
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
                        $model->save();
                        
                    }
                
                }else
                    unset($list_payment_intentions_accountability[$key]);
            }
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
    }

}
