<?php

namespace app\components\workflow;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 18/09/15
 * Time: 12:47
 */

class Workflow
{
    public static function changeState($object, $new_state)
    {
        if ( array_key_exists("app\\components\\workflow\\WithWorkflow", class_uses($object) )!==false) {

            return $object->changeState($new_state);
        } else {
            return false;
        }
        return false;
    }
}