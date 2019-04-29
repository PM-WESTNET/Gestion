<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 5/08/16
 * Time: 12:40
 */

namespace app\modules\sale\components\CodeGenerator;

/**
 * Class CodeGeneratorFactory
 * Factory del generador de codigos.
 *
 * @package app\modules\sale\components\CodeGenerator
 */
class CodeGeneratorFactory
{
    private static $instance;

    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new CodeGeneratorFactory;
        }
        return self::$instance;
    }

    /**
     * @param $name
     * @return CodeGeneratorInterface
     * @throws Exception
     */
    public function getGenerator($class, $fullPath=false){
        $class = ( $fullPath ? $class : 'app\\modules\\sale\\components\\CodeGenerator\\impl\\' . $class );
        if(class_exists($class) && is_subclass_of($class, 'app\modules\sale\components\CodeGenerator\CodeGeneratorInterface')){
            return new $class;
        }else{
            throw new \Exception('Generator does not exist.' );
        }
    }
}