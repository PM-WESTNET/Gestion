<?php

namespace app\modules\westnet\isp\wispro;

use app\modules\westnet\isp\ApiProvider;
use app\modules\westnet\sequre\components\RestRequest;
use FTP\Connection;
use app\modules\westnet\models\Connection as ContractConnection;
use app\modules\westnet\models\Node;
use Yii;


/**
 * MikrotikRequest is the responsable script for api calls to the resources needed to update data of mikrotiks servers
 * for now , works with fiber tech only, the wifi type is already controlled by soldef in some other way??
 *
 * @author emilasheras
 */
class MikrotikRequest extends RestRequest implements ApiProvider
{
    private $connection;
    private $node;
    private $customer_code;
    const BASE_URL = 'api/v1/fibra/UpdateRadiusPlanAPI';

    public function __construct($base_url, $token='')
    {
        parent::__construct($base_url, $token);
        parent::setOption(CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * Aplica cambios en el servidor Wispro
     * @return bool
     */
    public function apply()
    {
        $ip_nueva = long2ip($this->connection->ip4_1);
        $ip_anterior = (isset($this->connection->ip4_1_old)) ? long2ip($this->connection->ip4_1_old) : $ip_nueva;
        $data = array(
            'ip_anterior' => $ip_anterior,
            'ip_nueva' => $ip_nueva,
            'nodo' => $this->node->name,
            'codigo' => $this->customer_code,
        );
        
        //Creates a new request for a client view
        $response = parent::getRequest(MikrotikRequest::BASE_URL, RestRequest::METHOD_PUT, $data, false, true);
        // var_dump($response);
        // die();

        if($response['code'] == 200 ) {
            if((!Yii::$app instanceof Yii\console\Application) && is_string($response['rawResponse'])) {
                Yii::$app->session->addFlash('success', $response['rawResponse']);
            }
            return true;
        } else {
            if((!Yii::$app instanceof Yii\console\Application) && is_string($response['rawResponse'])) {
                Yii::$app->session->addFlash('error', $response['rawResponse']);
            }
            return false;
        }
    }

    public function loadConnection(ContractConnection $connection){
        $this->connection = $connection;
        $this->node = Node::findOne($connection->node_id);
        $this->customer_code = $connection->contract->customer->code;
        return true;
    }
}