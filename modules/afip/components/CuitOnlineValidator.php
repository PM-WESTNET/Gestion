<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 27/06/18
 * Time: 10:54
 */

namespace app\modules\afip\components;
use app\modules\invoice\components\einvoice\afip\Afip;

/**
 * Class CuitOnlineValidator
 * Buscamos
 *
 * @package app\modules\afip\components
 */
class CuitOnlineValidator extends Afip
{

    protected $serviceName = "ws_sr_padron_a4";
    protected $url = [
        'prod' => [
            "wsdl" => "https://aws.afip.gov.ar/sr-padron/webservices/personaServiceA4?WSDL",
            "file" => "/personaServiceA4.wsdl"
        ],
        'testing' => [
            "wsdl" => "https://awshomo.afip.gov.ar/sr-padron/webservices/personaServiceA4?WSDL",
            "file" => "/personaServiceA4-testing.wsdl"
        ]
    ];
    /**
     * Returns the Authentication values for the calls to the afip ws.
     *
     * @return array
     */
    public function getAuthToken()
    {
        return [
            'token' => $this->tokens[$this->company->tax_identification]['token'],
            'sign'  => $this->tokens[$this->company->tax_identification]['sign'],
            'cuitRepresentada'  => str_replace('-', '' , $this->company->tax_identification)
        ];
    }

    /**
     * Returns the version of the API.
     *
     * @return mixed
     */
    public function getVersion()
    {
        return "0.0.1";
    }

    /**
     * Call the soap and parse the response.
     *
     * @param $request      Function to execute in the ws.
     * @param array $body Parameters of the service function.
     * @return mixed
     * @throws \Exception
     */
    protected function soapCall($request, array $body=[])
    {
        $response = array();
        $params = array();
        try{
            $auth = $this->getAuthToken();
            $auth['idPersona'] = str_replace( '-', '',  $body['idPersona']);

            $params[$request] = $auth;
            \Yii::debug($auth);
            \Yii::debug($params);
            $response = $this->_soapClient->__soapCall($request, $params);
            \Yii::debug($response);

            \Yii::debug("RESPONSE ".print_r($response,1));

            $this->parseErrors($response);
            $this->parseObservations($response);
            $this->parseEvents($response);
            //$this->saveMessages();
        } catch(\Exception $ex){
            \Yii::debug("mendaje: " .$ex->getMessage() ." " . $ex->getFile() . " - " . $ex->getLine());
            \Yii::debug($ex->getTraceAsString());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->saveCalls) {
            //$this->saveCall($request, $params, $response);
        }
        return $response;
    }

    /**
     * Run the creation of the invoice.
     *
     * @param $object
     * @return mixed
     */
    public function run($object)
    {
        // TODO: Implement run() method.
    }

    /**
     * Returns true or false if the service is availeable.
     *
     * @return boolean
     */
    public function serviceAvailable()
    {
        $file_headers = @get_headers($this->getUrl());
        return (strpos($file_headers[0],"200"));
    }

    public function validate($cuit)
    {
        $function_name = "getPersona";
        $array = '';
        try {
            $result = $this->soapCall($function_name, ['idPersona' => $cuit]);
            if ($result->personaReturn) {
                $array = json_decode(json_encode($result), true);
                return $array;
            } else if ($result == -1) {
                throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function' => $function_name]));
            } else {
                throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function' => $function_name]));
            }
            return $array;
        } catch (\Exception $ex) {
            \Yii::debug($ex->getMessage());
            throw $ex;
        }
    }

    public function extractData($array)
    {
        $lastname = '';
        $name = '';
        $address = [
            'province' => '',
            'location' => '',
            'address' => ''
        ];
        $tax_id = '';
        $legal_name = '';

        if (array_key_exists('personaReturn', $array)) {
            $primero = true;
            if (array_key_exists('persona', $array['personaReturn'])) {

                if (array_key_exists('apellido', $array['personaReturn']['persona'])) {
                    $lastname = $array['personaReturn']['persona']['apellido'];
                }
                if (array_key_exists('nombre', $array['personaReturn']['persona'])) {
                    $name = $array['personaReturn']['persona']['nombre'];
                }
                if (array_key_exists('domicilio', $array['personaReturn']['persona'])) {
                    $address = $this->extractAddress($array['personaReturn']['persona']['domicilio']);
                }
                if (array_key_exists('impuesto', $array['personaReturn']['persona'])) {
                    $tax_id = $this->extractTax($array['personaReturn']['persona']['impuesto']);
                }
                if (array_key_exists('razonSocial', $array['personaReturn']['persona'])) {
                    $legal_name = $array['personaReturn']['persona']['razonSocial'];
                }
            }
        }

        $final_data = [
            'lastname' => $lastname,
            'name' => $name,
            'address' => $address,
            'tax_id' => $tax_id,
            'legal_name' => $legal_name,
            'array' => $array
        ];
        return $final_data;
    }

    public function extractAddress($array_addresses)
    {
        \Yii::trace($array_addresses);
        $province = '';
        $location = '';
        $dir_address = '';

        foreach ($array_addresses as $array_address) {
            \Yii::trace($array_address);
            \Yii::trace(is_array($array_address));
            if (is_array($array_address)) {
                if (array_key_exists('tipoDomicilio', $array_address)) {
                    if ($array_address['tipoDomicilio'] == 'FISCAL') {
                        if (array_key_exists('descripcionProvincia', $array_address)) {
                            $province = $array_address['descripcionProvincia'];
                        }
                        \Yii::trace(array_key_exists('localidad', $array_address));
                        if (array_key_exists('localidad', $array_address)) {
                            $location = $array_address['localidad'];
                        }
                        \Yii::trace(array_key_exists('direccion', $array_address));

                        if (array_key_exists('direccion', $array_address)) {

                            $dir_address = $array_address['direccion'];
                        }
                    }
                }
            }
        }

        if ($province == '' && $location == '' && $dir_address == '') {
            \Yii::trace('vacio');
            \Yii::trace(is_array($array_addresses));
            if (array_key_exists('tipoDomicilio', $array_addresses)) {

                if (array_key_exists('descripcionProvincia', $array_addresses)) {
                    $province = $array_addresses['descripcionProvincia'];
                }
                if (array_key_exists('localidad', $array_addresses)) {
                    $location = $array_addresses['localidad'];
                }
                if (array_key_exists('direccion', $array_addresses)) {
                    $dir_address = $array_addresses['direccion'];
                }
            }
        }

        $address = [
            'province' => $province,
            'location' => $location,
            'address' => $dir_address
        ];
        \Yii::trace($address);

        return $address;
    }

    /**
      Código de impuestos segun http://www.sistemasagiles.com.ar/trac/wiki/PadronContribuyentesAFIP#Impuestos
      20: MONOTRIBUTO
      30: IVA INSCRIPTO
      32: IVA EXENTO
      33: IVA NO INSCRIPTO
     */
    public function extractTax($array_taxs)
    {
        $tax_id = '';
        $tax_name = '';

        foreach ($array_taxs as $array_tax) {
            if (array_key_exists('estado', $array_tax)) {
                if ($array_tax['estado'] == "ACTIVO") {
                    if (array_key_exists('idImpuesto', $array_tax)) {
                        if ($array_tax['idImpuesto'] == 20) {
                            $tax_name = 'Monotributista';
                        }
                        if ($array_tax['idImpuesto'] == 30) {

                            $tax_name = 'IVA Inscripto';
                        }
                        if ($array_tax['idImpuesto'] == 32) {
                            $tax_name = 'Exento';
                        };
                        if ($array_tax['idImpuesto'] == 33) {
                            $tax_name = 'IVA No inscripto';
                        }
                    }
                }
            }
        }
        if ($tax_name != '') {
            $tax = \app\modules\sale\models\TaxCondition::find()->where(['name' => $tax_name])->one();
            if ($tax) {
                $tax_id = $tax->tax_condition_id;
            }
        }

        return $tax_id;
    }

}
