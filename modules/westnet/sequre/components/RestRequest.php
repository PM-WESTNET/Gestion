<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 29/01/16
 * Time: 12:06
 */

namespace app\modules\westnet\sequre\components;


use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;

class RestRequest
{

    const METHOD_POST   = 'POST';
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD   = 'HEAD';

    private $base_url;
    private $token;
    private $_curl;
    private $_options = [];
    private $_defaultOptions = [
        CURLOPT_USERAGENT      => 'Yii2-Curl-Agent',
        CURLOPT_TIMEOUT        => 600,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_SSL_VERIFYHOST => false
    ];

    public function __construct($base_url, $token='')
    {
        $this->base_url = $base_url;
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options + $this->_defaultOptions;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * Set curl option
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        //set value
        if (in_array($key, $this->_defaultOptions) && $key !== CURLOPT_WRITEFUNCTION) {
            $this->_defaultOptions[$key] = $value;
        } else {
            $this->_options[$key] = $value;
        }

        //return self
        return $this;
    }

    /**
     * Return a single option
     *
     * @param string|integer $key
     * @return mixed|boolean
     */
    public function getOption($key)
    {
        //get merged options depends on default and user options
        $mergesOptions = $this->getOptions();

        //return value or false if key is not set.
        return isset($mergesOptions[$key]) ? $mergesOptions[$key] : false;
    }

    /**
     * Unset a single curl option
     *
     * @param string $key
     *
     * @return $this
     */
    public function unsetOption($key)
    {
        //reset a single option if its set already
        if (isset($this->_options[$key])) {
            unset($this->_options[$key]);
        }

        return $this;
    }

    /**
     * Creates a HTTP request for API calls
     * @param string $method
     * @param string $url
     * @param array $data
     * @return Request
     */
    public function getRequest($url, $method=RestRequest::METHOD_GET, $data = [], $rawBody = false, $postJson=false)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        $headerOptions = [];

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $this->setOption(CURLOPT_NOBODY, true);
            $this->unsetOption(CURLOPT_WRITEFUNCTION);
        }

        if(is_array($data)) {
            $this->setOption(CURLOPT_POSTFIELDS, ($postJson ? json_encode($data) : http_build_query($data)) );
        }

        if ($this->_curl !== null) {
            curl_close($this->_curl); //stop curl
        }

        $this->_curl = curl_init($this->createAPIUrl($url));

        // Agrego el token si es necesario
        if(!is_null($this->token) && $this->token!='') {
            $headerOptions[] =  $this->getAuthorizationHeader();
        }
        if(!$rawBody) {
            $headerOptions[] =  'Content-Type: application/json';
        }

        if(isset(Yii::$app->params['curl_verbose'])) {
            $this->setOption(CURLOPT_VERBOSE, Yii::$app->params['curl_verbose']);
        }

        \Yii::info($headerOptions);
        $this->setOption(CURLOPT_HTTPHEADER, $headerOptions);

        curl_setopt_array($this->_curl, $this->getOptions());


        $body = curl_exec($this->_curl);

        error_log("wispro: " .$url . ": tiempo: " . curl_getinfo($this->_curl, CURLINFO_TOTAL_TIME));

        $response = [
            'code' => curl_getinfo($this->_curl, CURLINFO_HTTP_CODE),
        ];

        if(isset(Yii::$app->params['curl_verbose'])) {
            error_log(print_r($response,1));
        }

        try {
            $response['response']   = ($this->getOption(CURLOPT_CUSTOMREQUEST) === 'HEAD') ? true : ($rawBody ? $body : Json::decode((empty(trim($body)) ? '{}' : $body)) );
            $response['error']      = curl_error($this->_curl);
            $response['errorno']    = curl_errno($this->_curl);
        } catch(\Exception $ex) {
            $response['rawResponse'] = $body;
            $response['response'] = ($rawBody ? $body : Json::decode('{}') );
            $response['error']      = $ex->getMessage();
            $response['errorno']    = -1;
        }

        Yii::info($response);

        return $response;
    }

    /**
     * Creates a URL for making API requests
     * @param string $url
     * @return string
     */
    protected function createAPIUrl($url) {
        return $this->base_url. $url;
    }

    /**
     * Devuelve la header de autorización
     * Verificamos la base_url para saber si vamos a un wispro o a soldef
     * Si vamos a soldef la header de autorizacon es distinta a la del wispro
     */
    private function getAuthorizationHeader() {

        if (strpos($this->base_url, 'soldef') !== false) {
            return 'Authorization: Bearer ' . $this->token;
        }

        return 'Authorization: Token token=' . $this->token;
    }
}