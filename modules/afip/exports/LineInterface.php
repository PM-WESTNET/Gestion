<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/12/16
 * Time: 10:09
 */

namespace app\modules\afip\exports;


interface LineInterface {
    /**
     * @param $values
     * @return mixed
     */
    public function parse($values);

    /**
     * @return boolean
     */
    public function validate();
}
