<?php

namespace app\modules\invoice\components\einvoice\afip\mtxca;

use app\modules\invoice\components\einvoice\afip\Afip;

class Mtxca extends Afip
{

    protected $serviceName = "wsmtxca";
    protected $url = "https://serviciosjava.afip.gob.ar/wsmtxca/services/MTXCAService?wsdl";
    protected $urlTesting = "https://fwshomo.afip.gov.ar/wsmtxca/services/MTXCAService?wsdl";
    protected $excluyente_codes = [
        100,101,102,103,104,105,106,107,108,110,
        111,112,113,114,115,116,117,
        120,121,122,123,124,125,126,127,128,129,
        131,132,133,135,136,137,138,139,
        140,141,142,143,144,145,
        200,201,202,
        300,301,
        320,321,322,
        400,401,402,403,404,405,
        500,501,502,503,505,506,507,508,509,
        510,511,512,513,514,515,516,517,518,519,
        520,521,522,523,524,525,526,527,528,529,
        530,531,
        600,601,602,603,604,
        700,701,702,703,704,705,706,707,709,710,
        713,714,715,718,
        731,732,733,736,739,
        740,741,742,743,744,745,746,
        803,
        900,901,
        920,921,922,
        1000,1002,1003,
        1100,1101,1102,1103,1104,1105,1106,1107,1108,1109,1110,
        1111,1112,1121,1123,1124,1125,1126,1127,1128,1129,1130,
        1131,1132,
        1200,1201,1202,1203,1204,1205,1206,1207,1208,
        1300,1301,
        1400,
        1500,1501,1502,1503,
        1600,
        10010,10020,10021,10022,10024,10030,
    ];

    protected $noexcluyente_codes = [
        109,130,134,
        201,
        708,717,719,720,721,722,723,724,725,726,727,728,729,
        730,734,735,737,738,
        747,749,
        800,801,802,
        921,
        1001,1004,1005,
        1114,1115,1116,1117,1118,1119,1120,1122,

    ];

    public function getVersion()
    {
        return "0.1";
    }

    public function getAuthToken()
    {
        return [
            'authRequest'=>[
                'token' => $this->token,
                'sign' => $this->sign,
                'cuitRepresentada' => $this->customer['cuit']
            ]
        ];
    }

    public function parseErrors($response)
    {
        if ($response instanceof \stdClass) {
            $errors = null;
            try{
                if(isset($response->arrayErrores)) {
                    if (is_array($response->arrayErrores->codigoDescripcion)) {
                        foreach ($response->arrayErrores->codigoDescripcion as $key=>$value) {
                            $this->errors[] = [
                                'code' => $value->codigo,
                                'message' => $value->descripcion,
                            ];
                        }
                    } else {
                        $this->errors[] = [
                            'code' => $response->arrayErrores->codigoDescripcion->codigo,
                            'message' => $response->arrayErrores->codigoDescripcion->descripcion,
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
                if (isset($response->arrayObservaciones)) {
                    if( is_array($response->arrayObservaciones->codigoDescripcion ) ) {
                        foreach($response->arrayObservaciones->codigoDescripcion as $key=>$value) {
                            $this->observations[] = [
                                'code' => $value->codigo,
                                'message' => $value->descripcion,
                            ];
                        }
                    } else {
                        $this->observations[] = [
                            'code' => $response->arrayObservaciones->codigoDescripcion->codigo,
                            'message' => $response->arrayObservaciones->codigoDescripcion->descripcion,
                        ];
                    }
                }
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * Returns true or false if the service is availeable.
     *
     * @return boolean
     */
    public function serviceAvailable()
    {
        $file_headers = @get_headers($this->url);
        return (strpos($file_headers[0],"200"));
    }

    public function dummy()
    {
        $ret = $this->soapCall("dummy", []);
        return $ret;
    }

    public function run($object)
    {
        $dto = new InvoiceMtxca($object);

        $result = $this->soapCall("autorizarComprobante", $dto->getRequest(), true);
        if ( $this->rechaza() ) {
            return false;
        }

        $this->result = [
            'resultado'     => $result->autorizarComprobanteResponse->resultado,
            'cae'           => $result->autorizarComprobanteResponse->comprobanteResponse->CAE,
            'numero'        => $result->autorizarComprobanteResponse->comprobanteResponse->numeroComprobante,
            'vencimiento'   => $result->autorizarComprobanteResponse->comprobanteResponse->fechaVencimientoCAE,
        ];
        return true;
    }


    public function getAlicuotasIVA()
    {
        $result = $this->soapCall("consultarAlicuotasIVA", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result =  $this->parseResultTipos($result->arrayAlicuotasIVA->codigoDescripcion);
        return true;
    }

    public function getComprobante($codigoTipoComprobante, $numeroPuntoVenta, $numeroComprobante )
    {

        $result = $this->soapCall("consultarComprobante", ['consultaComprobanteRequest' => [
            'codigoTipoComprobante' => $codigoTipoComprobante,
            'numeroPuntoVenta'      => $numeroPuntoVenta,
            'numeroComprobante'     => $numeroComprobante
        ]]);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result =  $result;
        return true;
    }

    public function getCondicionesIVA()
    {
        $result = $this->soapCall("consultarCondicionesIVA", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayCondicionesIVA->codigoDescripcion);
        return true;
    }

    public function getCotizacionMoneda($codigoMoneda)
    {
        $result = $this->soapCall("consultarCotizacionMoneda", [
            'codigoMoneda' => $codigoMoneda
        ]);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = [
            'id' => $result->arrayCondicionesIVA->codigoDescripcion->codigo,
            'cotizacion' => $result->arrayCondicionesIVA->codigoDescripcion->descripcion,
            'fecha' => (new \DateTime("now"))->format("Y-m-d")
        ];
        return true;
    }

    public function getMonedas()
    {
        $result = $this->soapCall("consultarMonedas", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayMonedas->codigoDescripcion);
        return true;
    }

    public function getPuntosVenta()
    {
        $rta = array();
        $result = $this->soapCall("consultarPuntosVenta", []);
        if ( $this->rechaza() ) {
            return false;
        }
        if( property_exists( $result->arrayPuntosVenta, "puntoVenta" ) ) {
            foreach($result->arrayPuntosVenta->puntoVenta as $key => $value) {
                $rta[] = [
                    'numero'    => $value->numeroPuntoVenta,
                    'tipo'      => '',
                    'bloqueado' => $value->bloqueado,
                    'fechaBaja' => $value->fechaBaja
                ];
            }
        }
        $this->result = $rta;
        return true;
    }

    public function getPuntosVentaCAE()
    {
        $rta = array();
        $result = $this->soapCall("consultarPuntosVentaCAE", []);
        if ( $this->rechaza() ) {
            return false;
        }
        foreach($result->arrayPuntosVenta as $key => $value) {
            $rta[] = [
                'numero'    => $value->numeroPuntoVenta,
                'tipo'      => '',
                'bloqueado' => $value->bloqueado,
                'fechaBaja' => $value->puntoVenta
            ];
        }
        $this->result = $rta;
        return true;
    }

    public function getTiposComprobante()
    {
        $result = $this->soapCall("consultarTiposComprobante", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayTiposComprobante->codigoDescripcion);
        return true;
    }

    public function getTiposDatosAdicionales()
    {
        $result = $this->soapCall("consultarTiposDatosAdicionales", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayTiposDatosAdicionales->codigoDescripcion);
        return true;
    }

    public function getTiposDocumento()
    {
        $result = $this->soapCall("consultarTiposDocumento", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayTiposDocumento->codigoDescripcion);
        return true;
    }

    public function getTiposTributo()
    {
        $result = $this->soapCall("consultarTiposTributo", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayTiposTributo->codigoDescripcion);
        return true;
    }

    public function getUltimoComprobanteAutorizado($codigoTipoComprobante, $numeroPuntoVenta)
    {
        $result = $this->soapCall("consultarUltimoComprobanteAutorizado", [ 'consultaUltimoComprobanteAutorizadoRequest' => [
            'codigoTipoComprobante' => $codigoTipoComprobante,
            'numeroPuntoVenta' => $numeroPuntoVenta]
        ]);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = [
            'puntoDeVenta'      => $numeroPuntoVenta,
            'tipoComprobante'   => $codigoTipoComprobante,
            'numeroComprobante' => $result->numeroComprobante
        ];
        return true;
    }

    public function getUnidadesMedida()
    {
        $result = $this->soapCall("consultarUnidadesMedida", []);
        if ( $this->rechaza() ) {
            return false;
        }
        $this->result = $this->parseResultTipos($result->arrayUnidadesMedida->codigoDescripcion);
        return true;
    }

    public function getTiposConcepto()
    {
        return [
            "1" => "Productos",
            "2" => "Servicios",
            "3" => "Productos y Servicios"
        ];
    }

    private function parseResultTipos($data)
    {
        $result = array();
        if (is_array($data)) {
            foreach($data as $key=>$value){
                $result[] = [
                    'id'    => $value->codigo,
                    'desc'  => $value->descripcion,
                ];
            }
        } else {
            $result[] = [
                'id'    => $data->codigo,
                'desc'  => $data->descripcion,
            ];
        }
        return $result;
    }
}