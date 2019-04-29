<?php

namespace app\modules\westnet\notifications\components\scheduler\types;

/**
 *
 * @author mmoyano
 */
interface SchedulerInterface {
    public static function name();
    
    public static function description();
    
    public function mergeQuery(&$query);
}
