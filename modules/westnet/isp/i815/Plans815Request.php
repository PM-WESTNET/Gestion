<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/01/17
 * Time: 15:47
 */

namespace app\modules\westnet\isp\i815;

use app\modules\westnet\isp\ApiInterface;
use app\modules\westnet\isp\models\Client;
use app\modules\westnet\isp\Profiler;


class Plans815Request implements ApiInterface
{
    private $_plans = null;
    private $base_url  = '';
    private $internal_url = 'integracion/entrega/plan/';
    private $token;
    const Q_NAME    = 0;
    const Q_ID      = 1;

    public function __construct($base_url, $token='')
    {
        $this->token = $token;
        $this->base_url = $base_url;
    }

    /**
     * @throws \yii\web\HttpException
     */
    public function create($plan)
    {
        return null;
    }

    /**
     * Retorna un cliente del servidor, buscando por nombre.
     * @param $name
     * @return mixed
     */
    public function find($value, $type=Plans815Request::Q_NAME)
    {
        $planes = $this->listAll();

        if($planes) {
            Profiler::profile('find-planes-foreach');
            foreach($planes as $plan) {
                if( preg_replace("[ |/]", "-", strtolower($plan['plan']['name'])) == $value ) {
                    return $plan['plan'];
                }
            }
            Profiler::profile('find-planes-foreach');
        } else {
            return false;
        }
    }

    /**
     * Retorna un cliente del servidor, buscando por nombre.
     * @param $name
     * @return mixed
     */
    public function listAll()
    {
        if($this->_plans === null) {
            Profiler::profile('planes-xml');
            $response = (new CurlXml( $this->base_url, $this->token))->request($this->internal_url . 'listar/');
            Profiler::profile('planes-xml');

            Profiler::profile('planes-xml-parse');
            if($response && $response instanceof \SimpleXMLElement) {
                $planes =  [];
                foreach ($response->object as $xml) {
                    $planes[] = ['plan' => [
                        'id' => $xml['pk'],
                        'name' => $xml->field[0],
                    ]];
                }
                $this->_plans = $planes;
            }
            Profiler::profile('planes-xml-parse');
        }

        return $this->_plans;
    }

    /**
     * @inheritdoc
     * @param $plan
     */
    public function update($plan)
    {
    }

    /**
     * inheritdoc
     * @param $plan_id
     * @return bool
     */
    public function delete($plan_id)
    {
    }
}
