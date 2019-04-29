<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/01/17
 * Time: 15:04
 */

namespace app\modules\westnet\isp\i815;


class CurlXml
{
    private $curl_handler;
    private $url_base;
    private $token;

    public function __construct($url_base, $token=null)
    {
        $this->url_base = $url_base;
        $this->token = $token;
    }

    public function request($url, $params = [])
    {
        $this->curl_handler = curl_init();
        if(!is_null($this->token)) {
            $params['token'] = $this->token;
        }
        $url = $this->url_base . $url . '?' . http_build_query($params) ;

        $options = [
            CURLOPT_USERAGENT       => 'Yii2-Curl-Agent',
            CURLOPT_TIMEOUT         => 15,
            CURLOPT_CONNECTTIMEOUT  => 15,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_HTTPHEADER      => array('Content-Type: text/xml'),
            CURLOPT_URL             => $url,
            CURLOPT_SSL_VERIFYPEER  => false
        ];
        if(isset(\Yii::$app->params['curl_verbose'])) {
            $options[CURLOPT_VERBOSE] = \Yii::$app->params['curl_verbose'];
        }

        curl_setopt_array($this->curl_handler, $options);

        $attempts = 5;
        do {
            $result = $this->_request();
            $attempts--;
        } while(!$result && $attempts > 0);


        curl_close($this->curl_handler);

        return simplexml_load_string($result);
    }

    private function _request() {
        $result = curl_exec($this->curl_handler);
        if(($error_nro = curl_errno($this->curl_handler))!=0) {

            return false;
        }
        return $result;
    }
}