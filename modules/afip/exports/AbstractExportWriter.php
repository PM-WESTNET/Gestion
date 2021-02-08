<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:08
 */

namespace app\modules\afip\exports;


use app\modules\afip\exports\Exception\InvalidLineException;

abstract class AbstractExportWriter implements ExportFileWriterInterface
{

    protected $data = null;

    protected $lines = [];

    /**
     * @return mixed
     */
    public abstract function parse();

    /**
     * AbstractExportWriter constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    protected function addLine(LineInterface $line, $values)
    {
        $line->parse($values);
        if($line->validate()) {
            $this->lines[] = $line;
        } else {
            throw new InvalidLineException('The line: ' . count($this->lines) . " is invalid.");
        }
    }

    public function writeFile($filename)
    {
        ob_start();
        $file = fopen($filename, '+w');

        foreach ($this->lines as $line) {
            fwrite($file, $line->getLine()."\r\n");
        }

        fclose($file);
    }
}