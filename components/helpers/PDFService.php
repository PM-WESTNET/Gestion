<?php
namespace app\components\helpers;

use Yii;
use yii\base\Component;
use GuzzleHttp\Client;

/**
 * Description of PDFService
 *
 * @author mmoyano
 */
class PDFService extends Component{

    public static function makePdf($view)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, \app\modules\config\models\Config::getValue('wkhtmltopdf_docker_host'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    
        curl_setopt($ch, CURLOPT_PORT,  \app\modules\config\models\Config::getValue('wkhtmltopdf_docker_port'));
        
        $data = [
            'html' => $view,
            'options' => array(
            ),
        ];
        
        $data_str = http_build_query($data);
        
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . mb_strlen($data_str),
            'Expect:'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        
        $result = curl_exec($ch);
        
        curl_close($ch);
        
        return $result;
    }
}
