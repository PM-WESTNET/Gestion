<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 7/06/17
 * Time: 11:33
 */

namespace app\modules\westnet\isp;


class Profiler
{
    private static $times = [];
    private static $start = 0;

    public static function init()
    {
        self::$times = [];
        self::$start = 0;
    }

    public static function profile($quien)
    {
        if(self::$start) {
            if(!array_key_exists($quien, self::$times)) {
                self::$times[$quien] = 0;
            }
            self::$times[$quien] += microtime(true) - self::$start;
            self::$start  = 0;
        } else {
            self::$start = microtime(true);
        }
        error_log($quien . " ". (new \Datetime('now'))->format('d/m/Y H:i:s'));
    }

    public static function printTimes($error_log)
    {
        if($error_log) {
            error_log("--------------------------------------------------------");
        } else {
            echo "--------------------------------------------------------\n";
        }
        foreach(self::$times as $key=>$value) {
            if($error_log) {
                error_log(" - " . $key. ': ' . $value);
            } else {
                echo  " - " . $key. ': ' . $value."\n";
            }
        }
        if($error_log) {
            error_log("--------------------------------------------------------");
        } else {
            echo "--------------------------------------------------------\n";
        }

    }
}