<?php

namespace app\modules\westnet\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\SiroPaymentIntention;
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
    private function ObtainPaymentSurrenderApi($token,$date_from, $date_to, $cuit_administrator, $company_id){
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
        if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();

            $company = Company::find()->where(['company_id' => $request['company_id']])->one();
            $cuit_administrator = str_replace('-', '', $company->tax_identification);

            $token = $this->GetTokenApi($request['company_id']);
            

            $session = Yii::$app->session;
            $session->open();
            if(!$session->has('rendicion')){
                $surrender = $this->ObtainPaymentSurrenderApi($token, $request['date_from'], $request['date_to'], $cuit_administrator, $request['company_id']);
                $session['rendicion'] = $surrender;
            }

            $array_payments_to_process = [];
            foreach ($session['rendicion'] as $value) {
                $payment_date = (new \DateTime(substr($value, 0, 8)))->format('Y-m-d');
                $accreditation_date = (new \DateTime(substr($value, 8, 8)))->format('Y-m-d');
                $total_amount = (double) (substr($value, 24, 9) .'.'. substr($value, 33, 2));
                $customer_id = ltrim(substr($value, 35, 8), '0');
                $customer = Customer::findOne(['customer_id' => $customer_id]);
                $payment_method = substr($value, 44, 4);
                $siro_payment_intention_id = str_replace($customer->code, '', ltrim(substr($value, 103, 20), '0'));
                $collection_channel= substr($value, 123, 3);
                $rejection_code = substr($value, 126, 3);
                
                if($total_amount > 0){
                    $siro_payment_intention = SiroPaymentIntention::findOne(['siro_payment_intention_id' => $siro_payment_intention_id]);

                    if(isset($siro_payment_intention->estado) && $siro_payment_intention->estado != "PROCESADA")
                        $array_payments_to_process[] = [
                            'payment_date' => $payment_method,
                            'accreditation_date' =>  $accreditation_date,
                            'total_amount' =>  $total_amount,
                            'customer_id' => $customer_id,
                            'payment_method' => $payment_method,
                            'siro_payment_intention_id' => $siro_payment_intention_id,
                            'collection_channel' => $collection_channel,
                            'rejection_code' => $rejection_code

                        ];
                }


            }
            var_dump($array_payments_to_process);die();

            $session->close();

            
        }

        /*if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();
            $payment_intentions = Yii::$app->db->createCommand(
                                               'SELECT * FROM siro_payment_intention WHERE 
                                                status != "payed" AND 
                                                createdAt >= :date_from AND 
                                                createdAt <= :date_to AND
                                                payment_id IS NULL AND
                                                company_id = :company_id
                                            ')
                                              ->bindValue('date_from', $request['date_from'])
                                              ->bindValue('date_to', $request['date_to'])
                                              ->bindValue('company_id', $request['company_id'])
                                              ->queryAll();

            $token = ApiSiro::GetTokenApi($request['company_id']);
            $list_of_payments_to_process = [];
            foreach ($payment_intentions as $key => $value) {
                if(isset($value['hash'])){
                    $search_payment_intention = ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $value['hash']));

                    if(!isset($search_payment_intention['Message']) || isset($search_payment_intention['PagoExitoso']))
                        if($search_payment_intention['PagoExitoso'])
                            $list_of_payments_to_process[] = $search_payment_intention + $value;
                }
            }

            if(!empty($list_of_payments_to_process)){
                $dataProvider = new ArrayDataProvider([
                    'allModels' => $result,
                    'pagination' => [
                        'pageSize' => 15,
                    ],
                ]);
                return $this->render('index', ['dataProvider' => $dataProvider]);
            }else
                Yii::$app->session->setFlash("success", "No se han encontrado intenciones de pago que se encuentren procesadas.");
        }*/
        //die("fin");
        return $this->render('index');
    }


}
