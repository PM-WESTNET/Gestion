<?php

namespace app\modules\invoice\components\einvoice\afip\fev1;

use app\modules\invoice\components\einvoice\afip\Afip;
use app\modules\invoice\components\einvoice\InvoiceWsfev;
use Yii;

class Fev1 extends Afip
{

    protected $serviceName = "wsfe";
    protected $url = [
        'prod' => [
            "wsdl" => "https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL",
            "file" => "/wsfev1.wsdl"
        ],
        'testing' => [
            "wsdl" => "https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL",
            "file" => "/wsfev1-testing.wsdl"
        ]
    ];
    protected $excluyente_codes = [
        500,501,502,600,601,602,700,701,702,703,704,705,708,709,
        710,711,712,713,715,717,718,719,723,724,725,726,727,728,
        729,730,780,781,782,783,784,785,786,788,789,800,801,802,
        803,804,805,806,807,808,809,810,811,900,901,902,903,904,
        905,906,907,908,1000,1003,1006,1008,1009,1100,1101,1103,
        1104,1105,1106,1200,1201,1202,1203,1204,1205,1206,1207,
        1209,1300,1401,1402,1403,1404,1405,1406,1407,1408,1409,
        1411,1412,1413,1414,1415,1416,1417,1418,1419,1420,1421,
        1422,1423,1424,1425,1426,1427,1428,1429,1430,1431,1432,
        10000,10001,10002,10003,10004,10005,10006,10007,10008,
        10010,10011,10012,10013,10014,10015,10016,10017,10018,
        10019,10020,10021,10022,10023,10024,10025,10026,10027,
        10028,10029,10030,10031,10032,10033,10035,10036,10037,
        10038,10039,10040,10041,10042,10043,10044,10045,10046,
        10047,10048,10049,10051,10052,10053,10054,10055,10056,
        10057,10058,10059,10060,10061,10062,10063,10064,10065,
        10066,10067,10068,10069,10070,10075,10076,10077,10078,
        10079,10080,10081,10082,10083,10084,10085,10086,10087,
        10088,10089,10090,10091,10092,10093,10094,10095,10096,
        10097,10098,10099,10100,10101,10102,10104,10105,10110,
        10111,10112,10113,10114,10115,10116,10117,10118,10119,
        10120,10121,10122,10123,10124,10125,10126,10127,10128,
        10129,10130,10131,10132,10133,10134,10135,10136,10137,
        10138,10139,10140,10141,10142,10143,10144,10145,10146,
        10147,10148,10149,10150,10151,10200,10201,10202,11000,
        11001,12000,12001,15000,15001,15003,15004,15005,15006,
        15007,15008,15009,15010,15011,15012,15013,15100
    ];

    protected $noexcluyente_codes = [
        10063,10041,1006
    ];

    public function getVersion()
    {
        return "0.1";
    }

    public function getAuthToken()
    {
        return [
            'Auth'=>[
                'Token' => $this->tokens[$this->company->tax_identification]['token'],
                'Sign'  => $this->tokens[$this->company->tax_identification]['sign'],
                'Cuit'  => str_replace('-', '' , $this->company->tax_identification)
            ]
        ];
    }

    /**
     * Run the creation of the invoice.
     *
     * @param $dto
     * @return mixed
     */
    public function run($object)
    {
        try {
            $dto = new InvoiceFev($object);
            if ($dto->validate()) {
                $result = $this->soapCall("FECAESolicitar", $dto->getRequest(), true);
                if ( $this->rechaza() ) {
                    throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>'Solicitar CAE']));
                }
            } else {
                $this->errors = array_merge((is_array($this->errors) ? $this->errors : ($this->errors=='' ? [] : [$this->errors]) ), $dto->errors);
                throw new \Exception(Yii::t('afip', 'Validation fails.'));
            }

            $this->result = [
                'resultado'     => $result->FECAESolicitarResult->FeDetResp->FECAEDetResponse->Resultado,
                'cae'           => $result->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE,
                'numero'        => '',
                'vencimiento'   => $result->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAEFchVto
            ];
            return true;
        } catch (\Exception $ex) {
            $this->errors[] = [
                'code'      => $ex->getCode(),
                'message'   => $ex->getMessage()
            ];
            throw new \Exception(Yii::t('afip', 'Can\'t emit the voucher.'), $ex->getCode(), $ex);
        }
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


    /**
     * Ejecuta la funcion controlada contra el web service.
     *
     * @return array
     */
    private function exec($name, $function, $params)
    {
        try {
            $result = $this->soapCall($function, $params);
            if ( $this->rechaza() ) {
                $this->errors[] = [
                    'code'      => -1,
                    'message'   => Yii::t('afip', 'The function {function} is rejected.', ['function'=>$name])
                ];
                return -1;
            }
            $this->result = $result;
            return true;
        } catch(\Exception $ex) {
            $this->errors[] = [
                'code'      => $ex->getCode(),
                'message'   => $ex->getMessage()
            ];
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function dummy()
    {
        return $this->soapCall("FEDummy",[]);
    }

    /**
     * Retornas las alicuotas de IVA.
     *
     * @return array
     */
    public function getAlicuotasIVA()
    {
        $function_name = 'Alicuotas IVA';
        $result = $this->exec($function_name, "FEParamGetTiposIva", []);
        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposIvaResult->ResultGet->IvaTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }


    /**
     * Retorna un comprobante con todos sus datos.
     *
     * @param $tipoComprobante
     * @param $nroComprobante
     * @param $puntoDeVenta
     * @return mixed
     */
    public function getComprobante($tipoComprobante, $nroComprobante, $puntoDeVenta)
    {
        $function_name = 'Consultar Comprobante';
        $result = $this->exec($function_name, "FECompConsultar", [
            'FeCompConsReq' => [
                'CbteTipo' => $tipoComprobante,
                'CbteNro' => $nroComprobante,
                'PtoVta' => $puntoDeVenta
            ]
        ]);
        if($result) {
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    /**
     *
     * @return array
     */
    public function getCondicionesIVA()
    {
        $this->result = [];
        return true;
    }

    public function getCotizacionMoneda($monedaId)
    {
        $function_name = 'Cotizacion de Moneda';
        $result = $this->exec($function_name, "FEParamGetCotizacion", [
            'MonId' => $monedaId
        ]);
        if($result) {
            $this->result = [
                'id' => $this->result->FEParamGetCotizacionResult->ResultGet->MonId,
                'cotizacion' => $this->result->FEParamGetCotizacionResult->ResultGet->MonCotiz,
                'fecha' => $this->result->FEParamGetCotizacionResult->ResultGet->FchCotiz
            ];
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getMonedas()
    {
        $function_name = 'Monedas';
        $result = $this->exec($function_name, "FEParamGetTiposMonedas", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposMonedasResult->ResultGet->Moneda);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getPuntosVenta()
    {
        $function_name = 'Puntos de Venta';
        $result = $this->exec($function_name, "FEParamGetPtosVenta", []);

        if($result) {

            if( property_exists( $this->result->FEParamGetPtosVentaResult, "ResultGet" ) ) {
                foreach($this->result->FEParamGetPtosVentaResult->ResultGet as $key => $value) {
                    $rta[] = [
                        'numero'    => $value->Nro,
                        'tipo'      => $value->EmisionTipo,
                        'bloqueado' => $value->Bloqueado,
                        'fechaBaja' => $value->FchBaja
                    ];
                }
            }
            $this->result = $rta;

            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getTiposComprobante()
    {
        $function_name = 'Tipos de Comprobante';
        $result = $this->exec($function_name, "FEParamGetTiposCbte", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposCbteResult->ResultGet->CbteTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getTiposDatosAdicionales()
    {
        $function_name = 'Tipos de Datos Adicionales';
        $result = $this->exec($function_name, "FEParamGetTiposOpcional", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposOpcionalResult->ResultGet->OpcionalTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getTiposDocumento()
    {
        $function_name = 'Tipos de Documento';
        $result = $this->exec($function_name, "FEParamGetTiposDoc", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposDocResult->ResultGet->DocTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getTiposTributo()
    {
        $function_name = 'Tipos de Tributos';
        $result = $this->exec($function_name, "FEParamGetTiposTributos", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposTributosResult->ResultGet->TributoTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getUltimoComprobanteAutorizado($codigoTipoComprobante, $numeroPuntoVenta)
    {
        $function_name = 'Ultimo Comprobante autorizado';
        $result = $this->exec($function_name, "FECompUltimoAutorizado", [
            'PtoVta'    => $numeroPuntoVenta,
            'CbteTipo'  => $codigoTipoComprobante
        ]);

        if($result) {
            $this->result = [
                'puntoDeVenta'      => $this->result->FECompUltimoAutorizadoResult->PtoVta,
                'tipoComprobante'   => $this->result->FECompUltimoAutorizadoResult->CbteTipo,
                'numeroComprobante' => $this->result->FECompUltimoAutorizadoResult->CbteNro
            ];
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    public function getUnidadesMedida()
    {
        $this->result =  [];
        return true;
    }

    public function getTiposConcepto()
    {
        $function_name = 'Tipos de Concepto';
        $result = $this->exec($function_name, "FEParamGetTiposConcepto", []);

        if($result) {
            $this->result = $this->parseResultTipos($this->result->FEParamGetTiposConceptoResult->ResultGet->ConceptoTipo);
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    /**
     * Solo para Fev1
     */

    public function getTiposPaises()
    {
        $function_name = 'Tipos de Paises';
        $result = $this->exec($function_name, "FEParamGetTiposPaises", []);

        if($result) {
            $rta = array();
            foreach($this->result->FEParamGetTiposPaisesResult->ResultGet->PaisTipo as $key => $value) {
                $rta[] = [
                    'id'    => $value->Id,
                    'desc'  => $value->Desc,
                ];
            }
            $this->result = $rta;
            return true;
        } else if($result == -1) {
            throw new \Exception(Yii::t('afip', 'The function {function} is rejected.', ['function'=>$function_name]));
        } else {
            throw new \Exception(Yii::t('afip', 'Problem calling to WebService function: {function}.', ['function'=>$function_name]));
        }
    }

    private function parseResultTipos($data)
    {
        $result = array();
        if (is_array($data)) {
            foreach($data as $key=>$value){
                $result[] = [
                    'id' => $value->Id,
                    'desc' => $value->Desc,
                    'desde' => $value->FchDesde,
                    'hasta' => $value->FchHasta,
                ];
            }
        } else {
            $result[] = [
                'id' => $data->Id,
                'desc' => $data->Desc,
                'desde' => $data->FchDesde,
                'hasta' => $data->FchHasta,
            ];
        }
        return $result;
    }
}
