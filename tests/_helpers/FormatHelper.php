<?php

class FormatHelper {

    public static function GetFloat($str) {
        $str = str_replace("$", "", $str); // replace ',' with '.'

        $comma_position = strpos($str, ',');
        $point_position = strpos($str, '.');

        if ($comma_position > $point_position) {
            $str = str_replace(".", "", $str); // replace dots (thousand seps) with blancs
            $str = str_replace(",", ".", $str); // replace ',' with '.'            
        } else {
            $str = str_replace(",", "", $str); // replace commas (thousand seps) with blancs
        }
        
        return floatval($str); // take some last chances with floatval
    }

}
