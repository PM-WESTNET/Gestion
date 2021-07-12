<?php
namespace app\modules\westnet\notifications\components\siro;

use Yii;
use yii\base\Component;
use app\modules\config\models\Config;
use app\modules\sale\models\Bill;
use app\modules\westnet\notifications\models\SiroPaymentIntention;

class ApiSiro extends Component{
    /**
     * Return token access api
     */
	public static function GetTokenApi(){
		$username = Config::getConfig('siro_username');
		$password = Config::getConfig('siro_password');
		$url = Config::getConfig('siro_url_get_token');

        $conexion = curl_init();

        $datos = array(
            "Usuario" => $username->item->description,
            "Password" => $password->item->description
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

        curl_setopt($conexion, CURLOPT_URL,$url->item->description.'/'.$data['hash'].'/'.$data['id_resultado']);
        curl_setopt($conexion, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));

        curl_setopt($conexion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conexion, CURLOPT_CUSTOMREQUEST, 'GET'); 

    

        $respuesta=curl_exec($conexion);

        curl_close($conexion);

        return json_decode($respuesta,true);
	} 



	public static function CreatePaymentIntention($bill_id){

        $company_client_number = Config::getConfig('siro_company_client_number')->item->description;
        $invoice_concept = Config::getConfig('siro_invoice_concept')->item->description;
        $url_ok = Config::getConfig('siro_url_ok')->item->description;
        $url_error = Config::getConfig('siro_url_error')->item->description;
        $bill = Bill::findBillForId($bill_id);

        $referenciaOperacion = md5($bill->bill_id.'-'.$bill->date);
        $data = array(
            "nro_cliente_empresa" => str_pad($company_client_number, 19, '0', STR_PAD_LEFT),
            "nro_comprobante" => str_pad($bill_id, 20, '0', STR_PAD_LEFT),
            "Concepto" => $invoice_concept,
            "Importe" => $bill->total,
            "URL_OK" => $url_ok,
            "URL_ERROR" => $url_error,
            "IdReferenciaOperacion" => $referenciaOperacion,
            "Detalle" => [
                
            ]
        );



        $token = ApiSiro::GetTokenApi();
        $result = ApiSiro::CreatePaymentIntentionApi($token, $data);
        //var_dump($result,$token,$data);die();
        if(!isset($result['Message'])){
	        $paymentIntention = new SiroPaymentIntention;
	        $paymentIntention->bill_id = $bill_id;
	        $paymentIntention->hash = $result['Hash'];
	        $paymentIntention->reference = $referenciaOperacion;
	        $paymentIntention->url = $result['Url'];
	        $paymentIntention->createdAt = date('Y-m-d_H-i');
	        $paymentIntention->updatedAt = date('Y-m-d_H-i');
	        $paymentIntention->status = 'pending';
	        if($paymentIntention->save(false))
	        	return $result;
	    }

	    return false;

    }

    public static function SearchPaymentIntention($bill_id=null, $reference=null, $id_resultado=null){
    	if(isset($bill_id) && !isset($reference))
    		$paymentIntention = SiroPaymentIntention::find()->where(['bill_id' => $bill_id,'status' => 'pending'])->one();
    	else{
    		$paymentIntention = SiroPaymentIntention::find()->where(['reference' => $reference])->one();
    		if($paymentIntention){
    		   $token = ApiSiro::GetTokenApi();
    	       return ApiSiro::SearchPaymentIntentionApi($token, array("hash" => $paymentIntention->hash, 'id_resultado' => $id_resultado));
    	    }
    	}
    	
    	if($paymentIntention){
    	    return $paymentIntention;
    	}

    	return false;
    	


    }

}
