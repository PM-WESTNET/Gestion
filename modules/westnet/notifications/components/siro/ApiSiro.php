<?php
namespace app\modules\westnet\notifications\components\siro;

use Yii;
use yii\base\Component;
use app\modules\config\models\Config;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use app\modules\westnet\notifications\models\SiroPaymentIntention;

class ApiSiro extends Component{
    /**
     * Return token access api
     */
	public static function GetTokenApi($company_id){
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
     * Create a intention payment in API
     */
	public static function CreatePaymentIntentionApi($token,$data){
		$url = Config::getConfig('siro_url_create_payment_intention');
		$authorization = "Authorization: Bearer ".$token['access_token'];
        $conexion = curl_init();

        curl_setopt($conexion, CURLOPT_URL,$url->item->description);

        curl_setopt($conexion, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));

        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'POST'); 

        $respuesta=curl_exec($conexion);

        curl_close($conexion);

        return json_decode($respuesta,true);
	} 

    /**
     * Search intention payment created in BD of Siro
     */
	public static function SearchPaymentIntentionApi($token,$data){
		$url = Config::getConfig('siro_url_search_payment_intention');
		$authorization = "Authorization: Bearer ".$token['access_token'];
        $conexion = curl_init();

        curl_setopt($conexion, CURLOPT_URL,$url->item->description.'/'.$data['hash']);
        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));

        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'GET'); 

        $respuesta=curl_exec($conexion);

        curl_close($conexion);

        return json_decode($respuesta,true);
	} 



	public static function CreatePaymentIntention($customer){
        try{

            $company = Company::findOne(['company_id' => $customer->company_id]);
            $company_client_number = Config::getConfigForCompanyID('siro_company_client_number_'.$company->fantasy_name,$company->company_id)['description'];
            $invoice_concept = Config::getConfig('siro_invoice_concept')->item->description;
            $url_ok = Config::getConfig('siro_url_ok')->item->description;
            $url_error = Config::getConfig('siro_url_error')->item->description;

            $transaction = Yii::$app->db->beginTransaction();
            $paymentIntention = new SiroPaymentIntention;
            $paymentIntention->save(false);

            $referenciaOperacion = md5($paymentIntention->siro_payment_intention_id.'-'.$customer->customer_id.'-'.$customer->code);

            $invoice_concept = str_replace('@Cliente',$customer->code . ' ' . $customer->lastname,$invoice_concept);
            $invoice_concept = str_replace('(','',str_replace(')','',$invoice_concept));

            $data = array(
                "nro_cliente_empresa" => str_pad($customer->customer_id.$company_client_number, 19, '0', STR_PAD_LEFT),
                "nro_comprobante" => str_pad($paymentIntention->siro_payment_intention_id.$customer->code, 20, '0', STR_PAD_LEFT),
                "Concepto" => (strlen($invoice_concept) > 40) ? substr($invoice_concept, 0, 40) : $invoice_concept,
                "Importe" => abs($customer->current_account_balance),
                "URL_OK" => $url_ok,
                "URL_ERROR" => $url_error,
                "IdReferenciaOperacion" => $referenciaOperacion,
                "Detalle" => [ 
                ]
            );

            $token = ApiSiro::GetTokenApi($customer->company_id);
            $result = ApiSiro::CreatePaymentIntentionApi($token, $data);
        	
	    log_siro_payment_intention_without_error($result);    
	
            if(!isset($result['Message']) || isset($result['Url'])){
    	        $paymentIntention->customer_id = $customer->customer_id;
    	        $paymentIntention->hash = $result['Hash'];
    	        $paymentIntention->reference = $referenciaOperacion;
    	        $paymentIntention->url = $result['Url'];
    	        $paymentIntention->createdAt = date('Y-m-d H:i');
    	        $paymentIntention->updatedAt = date('Y-m-d H:i');
    	        $paymentIntention->status = 'pending';
                $paymentIntention->company_id = $company->company_id; 
    	        $paymentIntention->save(false);
                $transaction->commit();
    	        
                return $result;
    	    }
            $transaction->rollBack();
            log_siro_payment_intention($result);
    	    return false;
            
        } catch (Exception $e) {
            log_siro_payment_intention($e);
        }

    }

    public static function SearchPaymentIntention($reference, $id_resultado){
        $paymentIntention = SiroPaymentIntention::find()->where(['reference' => $reference])->one();
        if($paymentIntention){
            $token = ApiSiro::GetTokenApi($paymentIntention->company_id);
            return ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $paymentIntention->hash));
        }

    	return false;
    }
}
