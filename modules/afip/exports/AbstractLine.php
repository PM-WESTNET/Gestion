<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 13:04
 */

namespace app\modules\afip\exports;


abstract class AbstractLine implements LineInterface
{
    protected $line = '';
    protected $values = null;

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     * @return AbstractLine
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    /**
     * @return null
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param null $values
     * @return AbstractLine
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }
}