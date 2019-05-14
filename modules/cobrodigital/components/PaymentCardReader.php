<?php

namespace app\modules\cobrodigital\components;

use app\modules\cobrodigital\models\PaymentCardFile;
use Yii;

class PaymentCardReader
{
    public static function parse(PaymentCardFile $paymentCardFile)
    {
        $file = null;
        $datas = [];
        try {
            $file = fopen(Yii::getAlias('@webroot') ."/".$paymentCardFile->path, 'r');
            $i = 0;
            while ($line = fgets($file)) {
                if($i!=0) {
                    $line_data = explode(",", $line);
                    $data = [
                        'code_19_digits'       => $line_data[0],
                        'code_29_digits'        => $line_data[1],
                        'sap_apellido'          => $line_data[2],
                        'sap_identificador'     => $line_data[3],
                        'url'                   => $line_data[4],
                    ];
                    $datas[] = $data;
                }
                $i++;
            }
            $datas = array_slice($datas, 0, count($datas)-1);
        } catch (\Exception $ex){
            error_log($ex->getMessage());
        }
        if($file) {
            fclose($file);
        }
        return $datas;
    }
}