<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 30/07/15
 * Time: 16:18
 */

namespace app\modules\accounting\components;


interface CountableInterface
{
    public function getConfig();

    public function getAmounts();
}
