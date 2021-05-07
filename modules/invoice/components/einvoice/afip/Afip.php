<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/06/15
 * Time: 14:21
 */

namespace app\modules\invoice\components\einvoice\afip;

use app\modules\invoice\components\einvoice\ApiBase;
use app\modules\invoice\models\MessageLog;
use ReflectionClass;
use SimpleXMLElement;
use SoapFault;
use SoapVar;
use Yii;
use yii\base\Exception;

abstract class Afip extends ApiBase
{
    protected $_soapClient;
    protected $url = [];
    protected $urlAuth = [
        'prod' => [
            "wsdl" => "https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl",
            "file" => "auth.wsdl",
        ],
        'testing' => [
            "wsdl" => "https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl",
            "file" => "auth-testing.wsdl",
        ]
    ];
    protected $token;
    protected $sign;
    protected $company;
    protected $expirationTime;
    protected $result;
    protected $excluyente_codes;
    protected $noexcluyente_codes;
    protected $serviceName;
    protected $testing = false;
    protected $useOnline = true;
    protected $tokens = [];
    protected $saveCalls = false;

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseOnline()
    {
        return $this->useOnline;
    }

    /**
     * @param boolean $useOnline
     */
    public function setUseOnline($useOnline)
    {
        $this->useOnline = $useOnline;
    }

    /**
     * @return boolean
     */
    public function isSaveCalls()
    {
        return $this->saveCalls;
    }

    /**
     * @param boolean $saveCalls
     * @return Afip
     */
    public function setSaveCalls($saveCalls)
    {
        $this->saveCalls = $saveCalls;
        return $this;
    }


    /**
     * Retorna todas las tokens guardadas.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Setea el token de autenticacion a los WebServices de la afip.
     *
     * @param $token
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }




    /**
     * Returns the Service name.
     *
     * @return array
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Retorna el resultado de la funcion llamada por el Web Service.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }


    /**
     * @return mixed
     */
    public function getTesting()
    {
        return $this->testing;
    }

    /**
     * @param mixed $testing
     * @return Afip
     */
    public function setTesting($testing)
    {
        $this->testing = $testing;
        return $this;
    }


    /**
     * Comprueba si los errores obtenidos son excluyentes.
     *
     * @return bool
     */
    protected function rechaza()
    {
        $ret = false;
        if(!empty($this->errors)) {
            foreach($this->errors as $key=>$value) {
                $ret = (array_search($value['code'], $this->excluyente_codes )  !== false ? true : $ret );
            }
        }
        return $ret;
    }


    /**
     * Retorna si el token de autenticacion  a los WebServices de la afip es valido o no.
     *
     * @return bool
     */
    public function isTokenValid()
    {
        return (
            array_key_exists($this->company->tax_identification, $this->tokens) &&
            !empty($this->tokens[$this->company->tax_identification]['sign']) &&
            $this->tokens[$this->company->tax_identification]['expirationTime'] > date('c',date('U'))
        );
    }

    /**
     * Función básica que parsea los errores del resultado de la funcion llamada del webservice.
     *
     * @param $response
     */
    public function parseErrors($response)
    {
        if ($response instanceof \stdClass) {
            $this->errors = array();
            try{
                foreach(get_object_vars($response) as $key=>$value) {
                    foreach ($response->$key->Errors as $kError=>$vError) {
                        $this->errors[] = [
                            'code' => $vError->Code,
                            'message' => Yii::t('afip',  $vError->Msg),
                        ];
                    }
                }
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * Función básica que parsea las observaciones del resultado de la funcion llamada del webservice.
     *
     * @param $response
     */
    public function parseObservations($response)
    {
        if ($response instanceof \stdClass) {
            $this->observations = array();
            try{
                foreach(get_object_vars($response) as $key=>$value) {
                    // TODO Esta solo para la factura en teoria, ver si es la misma respuesta en los otros
                    if(isset($response->$key->FeDetResp)) {
                        if(is_array($response->$key->FeDetResp->FECAEDetResponse->Observaciones->Obs)){
                            foreach ($response->$key->FeDetResp->FECAEDetResponse->Observaciones->Obs as $kObs=>$vVal) {
                                $this->observations[] = [
                                    'code' => $vVal->Code,
                                    'message' => Yii::t('afip', $vVal->Msg),
                                ];
                            }
                        } else {
                            $this->observations[] = [
                                'code' => $response->$key->FeDetResp->FECAEDetResponse->Observaciones->Obs->Code,
                                'message' => Yii::t('afip', $response->$key->FeDetResp->FECAEDetResponse->Observaciones->Obs->Msg),
                            ];
                        }
                    }
                }
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * Función básica que parsea los eventos del resultado de la funcion llamada del webservice.
     *
     * @param $response
     */
    public function parseEvents($response)
    {
        if ($response instanceof SimpleXMLElement) {
            foreach ($response->Events as $key=>$value) {
                $this->events[] = [
                    'code' => $value->Code,
                    'message' => Yii::t('afip', $value->Msg),
                ];
            }
        }
    }

    /**
     * Returns the Authentication values for the calls to the afip ws.
     *
     * @return array
     */
    public abstract function getAuthToken();

    /**
     * Returns the encripted authentication string.
     *
     * @param $cert         Certificate
     * @param $privateKey   Private key
     * @param $passPhrase   Phrase of the private key
     * @param $service      Service
     * @return array
     */
    private function signKey($cert, $privateKey, $passPhrase, $service, $expirationDate=null)
    {
        $msg="";
        if($expirationDate==null) {
            $this->expirationTime = date('c',date('U')+3600);
        } else {
            $this->expirationTime = $expirationDate;
        }
        // Se arma el xml de validacion y tiempos de validez.
        $fileName = sys_get_temp_dir() . "/" . rand();
        $xml = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0"></loginTicketRequest>');
        $xml->addChild('header');
        $xml->header->addChild('uniqueId',date('U'));
        $xml->header->addChild('generationTime',date('c',date('U')-60));
        $xml->header->addChild('expirationTime',$this->expirationTime);
        $xml->addChild('service',$service);
        $xml->asXML($fileName.'.xml');

        try {
            // Se encripta el mensaje y se guarda en archivos temporales.
            // La funcion openssl_pkcs7_sign no permite hacerlo en memoria.
            $sign = openssl_pkcs7_sign($fileName . ".xml", $fileName . ".tmp",
                "file://$cert",
                array("file://$privateKey", $passPhrase),
                array(), !PKCS7_DETACHED);

            // Si se puedo encriptar, se lee el archivo y se borran los temporales.
            if ($sign) {
                $file = fopen($fileName.".tmp", "r");
                $i=0;
                while (!feof($file)){
                    $buffer = fgets($file);
                    if ( $i++ >= 4 ) {
                        $msg .= $buffer;
                    }
                }
                fclose($file);
                unlink($fileName.".tmp");
                unlink($fileName.".xml");
            } else {
                throw new \Exception(Yii::t('afip', 'Certificate exception. Couldn\'t sign the message.').".");
            }
        }catch (\Exception $ex){
            throw new \Exception(Yii::t('afip', 'Certificate exception. Couldn\'t sign the message.'), $ex->getCode(), $ex);
        }
        return $msg;
    }

    /**
     * Authorize with the ws.
     *
     * @param $cert         Certificate
     * @param $privateKey   Private key
     * @param $passPhrase   Phrase of the private key
     * @param $expirationDate Date of expiration
     * @return bool
     */
    public function authorize($cert, $private, $phrase, $expirationDate = null){
        ini_set("soap.wsdl_cache_enabled", 0);
        ini_set('soap.wsdl_cache_ttl',0);

        try {
            $wsdl = $this->getAuthUrl();

            if($this->useOnline) {
                if(!@file_get_contents($wsdl)) {
                    throw new Exception(Yii::t('afip', 'Can\'t connect to Authorization Web Services.'));
                }
            }
            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }
            error_log($wsdl);
            \Yii::trace($wsdl);
            $soapClient = new \SoapClient( $wsdl, array(
                'trace'          => 1,
                'exceptions'     => true,
            ));

            $msg = $this->signKey($cert, $private, $phrase, $this->getServiceName(), $expirationDate);
            $auth = $soapClient->loginCms(array('in0'=>$msg));
            if ($auth instanceof \SoapFault) {
                throw new \Exception($auth->getMessage());
            } else {
                $retXml = new SimpleXMLElement( $auth->loginCmsReturn );
                $this->token = trim($retXml->credentials->token->__toString());
                $this->sign  = trim($retXml->credentials->sign->__toString());

                $this->tokens[$this->company->tax_identification] = [
                    'token'             => $this->token,
                    'sign'              => $this->sign,
                    'expirationTime'    => $this->expirationTime,
                    'serviceName'       => $this->serviceName,
                ];

                return true;
            }

        } catch(\Exception $ex) {
            throw new \Exception(Yii::t('afip', 'Authorization failed. Msg: {message}', ['message'=>$ex->getMessage()]), $ex->getCode(), $ex);
        }
    }

    /**
     * Connect to ws.
     *
     * @param array $options
     * @return SoapClient
     * @throws \Exception
     */
    public function connect($options=[], $context_options=null, $soap_version = 'SOAP_1_2')
    {
        try {
            if ($this->testing) {
                $context_options = ($context_options ? $context_options : ["ssl" => ["ciphers" => "TLSv1"]] );
            } else {
                $context_options = ($context_options ? $context_options : [] );
            }

            $wsdl = $this->getUrl();
            $context = stream_context_create($context_options);

            if ($this->useOnline) {
                if (!@file_get_contents($wsdl, false, $context)) {
                    throw new Exception(Yii::t('afip', 'Can\'t connect to AFIP web services.'));
                }
            }
            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }
            $this->_soapClient = new \SoapClient($wsdl, array_merge([
                        'soap_version' => $soap_version,
                        'exceptions' => true,
                        'trace' => 1,
                        'stream_context' => $context,
                    ], $options
            ));
            if (!$this->_soapClient) {
                throw new \Exception(Yii::t('afip', "Service Unavailable."));
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Call the soap and parse the response.
     *
     * @param $request      Function to execute in the ws.
     * @param array $body   Parameters of the service function.
     * @return mixed
     */
    protected function soapCall($request, array $body=[])
    {
        $response = array();
        $params = array();
        try{
            $params[$request] = array_merge( $this->getAuthToken(), $body);
            $response = $this->_soapClient->__soapCall($request, $params);

            $this->parseErrors($response);
            $this->parseObservations($response);
            $this->parseEvents($response);
            $this->saveMessages();
        } catch(\Exception $ex){
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->saveCalls) {
            $this->saveCall($request, $params, $response);
        }
        return $response;
    }

    /**
     * Guarda en la base de datos los mensajes que retorna el webservice.
     *
     */
    private function saveMessages() {
        if($this->hasErrors()) {
            foreach($this->errors as $error) {
                $msg = new MessageLog();
                $msg->code = $error['code'];
                $msg->type = MessageLog::MESSAGE_ERROR;
                $msg->description = $error['message'];
                $msg->save();
            }
        }

        if($this->hasEvents()) {
            foreach($this->events as $event) {
                $msg = new MessageLog();
                $msg->code = $event['code'];
                $msg->type =  MessageLog::MESSAGE_EVENT;
                $msg->description = $event['message'];
                $msg->save();
            }
        }

        if($this->hasObservations()) {
            foreach($this->observations as $obs) {
                $msg = new MessageLog();
                $msg->code = $obs['code'];
                $msg->type = MessageLog::MESSAGE_OBSERVATION;
                $msg->description = $obs['message'];
                $msg->save();
            }
        }
    }

    private function saveCall($request, $params, $response)
    {
        error_log("-------------------------------------------------------------------------");
        error_log("-------------------------------------------------------------------------");
        error_log($request);
        error_log("-------------------------------------------------------------------------");
        error_log(print_r($params,1));
        error_log("-------------------------------------------------------------------------");
        error_log(print_r($response,1));

        error_log("-------------------------------------------------------------------------");
        error_log("-------------------------------------------------------------------------");
    }

    public function getAuthUrl()
    {
        return ($this->useOnline ? '' : __DIR__ .'/' ) . ($this->urlAuth[(!$this->testing?'prod':'testing')][($this->useOnline?'wsdl':'file')]);
    }

    public function getUrl()
    {
        $reflectionClass = new ReflectionClass($this);
        return ($this->useOnline ? '' : dirname($reflectionClass->getFileName()) .'/' ) . ($this->url[(!$this->testing?'prod':'testing')][($this->useOnline?'wsdl':'file')]);
    }
}