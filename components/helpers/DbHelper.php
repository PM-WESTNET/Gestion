<?php

namespace app\components\helpers;

/**
 * Includes function for db data manipulation
 *
 * @author marcelo
 */
class DbHelper {

    /**
     * return database name of a connection
     * 
     * @param type $connection
     * @return string
     */
    public static function getDbName($connection) {
        $dsn = $connection->dsn;
        if (preg_match('/' . 'dbname' . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public static function getDbHost($connection) {
        $dsn = $connection->dsn;
        if (preg_match('/' . 'host' . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public static function getDbPort($connection) {
        $dsn = $connection->dsn;
        if (preg_match('/' . 'port' . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

}
