<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 17/09/20
 * Time: 17:01
 */

namespace app\modules\westnet\components;

use Yii;
use app\modules\westnet\models\NodeChangeProcess;

/**
 * Class ChangeNodeReader
 * @package app\modules\westnet\components
 */
class ChangeNodeReader
{
    public function parse(NodeChangeProcess $node_change_process)
    {
        $file = null;
        $datas = [];
        try {
            $file = fopen(Yii::getAlias('@webroot') ."/".$node_change_process->input_file, 'r');
            $i = 0;
            while ($line = fgets($file)) {
                if($i!=0) {
                    $array_line = explode(',', $line);
                    $data = [
                        'ip'   => $array_line[0],
                        'node_name' => $array_line[1],
                        'contract_id' => $array_line[2],
                    ];

                    $datas[] = $data;
                }
                $i++;
            }
        } catch (\Exception $ex){
            error_log($ex->getMessage());
        }
        if($file) {
            fclose($file);
        }
        return $datas;
    }
}