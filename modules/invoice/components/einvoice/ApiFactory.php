<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 19/06/15
 * Time: 14:21
 */

namespace app\modules\invoice\components\einvoice;

class ApiFactory {

    private static $instance;

    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new APIFactory;
        }
        return self::$instance;
    }

    /**
     * @param $name
     * @return ApiInterface
     * @throws Exception
     */
    public function getApi($class){
        //$class = '\app\modules\invoice\components\einvoice\\'.$package.'\\'.$name.'\\'.ucfirst($name);
        if(class_exists($class) && is_subclass_of($class, 'app\modules\invoice\components\einvoice\ApiBase')){
            return new $class;
        }else{
            throw new \Exception('API does not exist.' );
        }
    }
}