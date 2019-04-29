<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/02/16
 * Time: 17:27
 */

namespace app\components\helpers;

use yii\log\Logger;

class ApacheLogger extends Logger
{
    public function log($message, $level, $category = 'application')
    {
        error_log($message);
    }
}