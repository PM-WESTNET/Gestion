<?php

namespace app\components\helpers;

use Yii;

class FileLog {

    /*
        Inserta contenido a un archivo para proposito de log. El archivo se intenta crear si no existe

    */
    public static function addLog ($file, $content) {
        $filePath = Yii::getAlias('@runtime') . '/logs/'. $file . '.txt';

        $resource = fopen($filePath, 'a+');

        if ($resource) {
            fwrite($resource, "\n");
            fwrite($resource, date('d-m-Y H:m:s') . '   '. $content);

            fclose($resource);
        }
    }
}