<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:06
 */

namespace app\modules\afip\exports;

/**
 * Interface ExportFileWriterInterface
 * Se debe implementar cuando se necesita crear un writer para exportaciones.
 *
 * @package app\modules\afip\exports
 */
interface ExportFileWriterInterface
{
    /**
     * @param $filename
     * @return mixed
     */
    public function writeFile($filename);

    /**
     * @return mixed
     */
    public function parse();
}