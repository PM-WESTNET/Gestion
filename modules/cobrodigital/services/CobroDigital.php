<?php

namespace app\modules\cobrodigital\services;

use app\modules\sale\models\Customer;
use Yii;
use yii\httpclient\Client;
use app\modules\config\models\Config;

/**
 * Class CobroDigital
 * @package app\modules\cobrodigital\services
 * Se Construye a partir del documento de WebServices 3
 */
class CobroDigital
{
    //Metodos del ws3
    const METHOD_CREAR_PAGADOR = 'crear_pagador';
    const METHOD_EDITAR_PAGADOR = 'editar_pagador';
    const METHOD_CONSULTAR_TRANSACCIONES = 'consultar_transacciones';
    const METHOD_GENERAR_BOLETA = 'generar_boleta';
    const METHOD_INHABILITAR_BOLETA = 'inhabilitar_boleta';
    const METHOD_OBTENER_CODIGO_BARRAS_DE_BOLETA = 'obtener_codigo_de_barras';
    const METHOD_OBTENER_CODIGO_PAGADOR = 'obtener_codigo_electronico';
    const METHOD_VERIFICAR_EXISTENCIA_PAGADOR = 'verificar_existencia_pagador';
    const METHOD_CONSULTAR_ACTIVIDAD_DE_MICROSITIO = 'consultar_actividad_micrositio';
    const METHOD_CONSULTAR_ESTRUCTURA_PAGADORES = 'consultar_estructura_pagadores';
    const METHOD_CONSULTAR_BOLETAS = 'consultar_boletas';
    const METHOD_OBTENER_BOLETA_HTML = 'obtener_boleta_html';

    //Keys de filtros para consulta de transacciones y boletas
    const FILTRO_NUMERO_DE_BOLETA = 'nro_boleta';
    const FILTRO_CONCEPTO = 'concepto';
    const FILTRO_IDENTIFICADOR = 'identificador';
    const FILTRO_NOMBRE = 'nombre';

    //Tipos de transacciones
    const TRANSACTION_TYPE_INGRESOS = 'ingresos';
    const TRANSACTION_TYPE_EGRESOS = 'egresos';
    const TRANSACTION_TYPE_DEBITO_AUTOMATICO = 'debito_automatico';
    const TRANSACTION_TYPE_TARJETA_DE_CREDITO = 'tarjeta_credito';


    /**
     * @param Customer $customer
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Crea una entidad pagador en el ws de Cobro digital
     */
    public static function crearPagador($customer_code, $customer_document_number, $customer_email)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_CREAR_PAGADOR,
                'pagador' => [
                    'numerocliente' => $customer_code,
                    'NroDocumento' => $customer_document_number,
                    'email' => $customer_email,
                ]
            ])
            ->send();

        if(!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return true;
    }

    /**
     * @param Customer $customer
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function editarPagador($customer_code, $new_customer_document_number, $new_customer_email)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_EDITAR_PAGADOR,
                'identificador' => 'numerocliente',
                'buscar' => $customer_code,
                'pagador' => [
                    'numerocliente' => $customer_code,
                    'NroDocumento' => $new_customer_document_number,
                    'email' => $new_customer_email,
                ]
            ])
            ->send();

        if($response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return true;
    }

    /**
     * @param $date_from fecha en formato YYYYMMDD
     * @param $date_to fecha en formato YYYYMMDD
     * @param $filtros array con el siguiente formato:
     *  [
     *     CobroDigital::FILTRO_NUMERO_DE_BOLETA => valor_numero_de_boleta,
     *     CobroDigital::FILTRO_CONCEPTO => valor_concepto_de_boleta,
     *     CobroDigital::FILTRO_IDENTIFICADOR => valor_identificador,
     *     CobroDigital::FILTRO_NOMBRE=> valor_nombre,
     *  ]
     * @param $offset integer
     * @param $limit integer
     * @param $tipo string. Valores Permitidos:
     *      CobroDigital::TRANSACTION_TYPE_INGRESOS,
     *      CobroDigital::TRANSACTION_TYPE_EGRESOS,
     *      CobroDigital::TRANSACTION_TYPE_DEBITO_AUTOMATICO,
     *      CobroDigital::TRANSACTION_TYPE_TARJETA_DE_CREDITO
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve un array de transacciones con las siguientes keys: id_transaccion, Fecha, Nro Boleta, Identificación, Nombre, Info, Concepto, Importe Bruto, Comisión, Importe neto, Saldo acumulado
     */
    public static function consultarTransacciones($date_from, $date_to, $filtros, $offset, $limit, $tipo)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_CONSULTAR_TRANSACCIONES,
                'desde' => $date_from,
                'hasta' => $date_to,
                'filtros' => $filtros,
                'offset' => $offset,
                'limit' => $limit,
                'tipo' => $tipo,
            ])
            ->send();

        if(!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $customer_code integer
     * @param $concept string. Concepto por el cual se solicita el pago.
     * @param $template string. Diseño de la boleta.
     * @param $due_dates array. Fechas en formato YYYYMMDD, la cantidad de fechas de vencimiento indicará la cantidad de codigos de barras que se generarán
     * @param $amount array. Debe corresponderse con la cantidad de fechas de vencimiento enviadas. Cada importe corresponderá a un vencimiento
     * @return bool
     * @throws \yii\base\InvalidConfigException
     *
     * IMPORTANTE: WS3 no emitirá mas de una boleta por pagador, con los mismos importes y fechas de vencimiento
     *
     * Genera una boleta con tantos codigos de barra como fechas de vencimientos se le indiquen.
     * Devuelve el número de boleta generada
     */
    public static function generarBoleta($customer_code, $concept, $template, $due_dates, $amount)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_GENERAR_BOLETA,
                'identificador' => 'numerocliente',
                'buscar' => $customer_code,
                'concepto' => $concept,
                'plantilla' => $template,
                'fechas_vencimiento' => $due_dates,
                'importes' => $amount,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $numero_de_boleta
     * @return bool
     * @throws \yii\base\InvalidConfigException
     *      Una vez generada una boleta, no puede editarse ni borrarse. Si se genera una boleta con datos que desea modificar,
     * deberá inhabilitarla y generar una nueva boleta. La misma acción aplica a si se desea inhabilitarla por cualquier otro motivo.
     *
     * Permite inhabilitar una boleta en específico
     */
    public static function inhabilitarBoleta($numero_de_boleta)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_INHABILITAR_BOLETA,
                'nro_boleta' => $numero_de_boleta,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return true;
    }

    /**
     * @param $numero_de_boleta
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve los codigos de barra para cada vencimiento(mínimo 1, máximo 4) - 29 Dígitos
     */
    public static function obtenerCodigoDeBarraDeUnaBoleta($numero_de_boleta)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_OBTENER_CODIGO_BARRAS_DE_BOLETA,
                'nro_boleta' => $numero_de_boleta,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $customer_code
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve el codigo electronico de un pagador - 19 Dígitos
     */
    public static function obtenerCodigoDeUnPagador($customer_code)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_OBTENER_CODIGO_PAGADOR,
                'identificador' => 'numerocliente',
                'buscar' => $customer_code,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $customer_code
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Indica si el pagador existe en CobroDigital
     */
    public static function verificarExistenciaPagador($customer_code)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_VERIFICAR_EXISTENCIA_PAGADOR,
                'identificador' => 'numerocliente',
                'buscar' => $customer_code,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $customer_code
     * @param $date_from fecha en formato YYYYMMDD
     * @param $date_to fecha en formato YYYYMMDD
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * WS3: Este método aplica a quienes tienen implementado un micrositio en alguna de las plataformas digitales en las que interactúan con sus pagadores.
     * Devuelve la actividad de un pagador en el micrositio
     */
    public static function consultarActividadMicrositio($customer_code, $date_from, $date_to)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_CONSULTAR_ACTIVIDAD_DE_MICROSITIO,
                'identificador' => 'numerocliente',
                'buscar' => $customer_code,
                'desde' => $date_from,
                'hasta' => $date_to,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve los campos correspondientes de la entidad pagador
     */
    public static function consultarEstructuraPagadores()
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_CONSULTAR_ESTRUCTURA_PAGADORES,
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $date_from fecha en formato YYYYMMDD
     * @param $date_to fecha en formato YYYYMMDD
     * @param $filtros array con el siguiente formato:
     *  [
     *     CobroDigital::FILTRO_NUMERO_DE_BOLETA => valor_numero_de_boleta,
     *     CobroDigital::FILTRO_CONCEPTO => valor_concepto_de_boleta,
     *     CobroDigital::FILTRO_IDENTIFICADOR => valor_identificador,
     *     CobroDigital::FILTRO_NOMBRE=> valor_nombre,
     *  ]
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve las boletas generadas en la cuenta en un array con los siguientes keys: Fecha_emision, identificacion, Nombre, numero_Boleta, concepto
     */
    public static function consultarBoletas($date_from, $date_to, $filtros)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_CONSULTAR_BOLETAS,
                'desde' => $date_from,
                'hasta' => $date_to,
                'filtros' => $filtros
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }

    /**
     * @param $numero_de_boleta
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * Devuelve HTML correspondiente a la boleta indicada
     */
    public static function obtenerBoletaHTML($numero_de_boleta)
    {
        $client = new Client();
        $url = Config::getValue('cobrodigital-url');
        $id = Config::getValue('cobrodigital-user');
        $sid = Config::getValue('cobrodigital-password');

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'idComercio' => $id,
                'sid' => $sid,
                'metodo_webservice' => self::METHOD_OBTENER_BOLETA_HTML,
                'nro_boleta' => $numero_de_boleta
            ])
            ->send();

        if (!$response['ejecucion_correcta']) {
            \Yii::$app->session->setFlash($response['log']);
            return false;
        }

        return $response['datos'];
    }
}