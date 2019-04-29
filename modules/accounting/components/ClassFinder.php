<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/08/15
 * Time: 16:51
 */

namespace app\modules\accounting\components;

use app\components\helpers\ClassFinderHelper;
use ReflectionClass;
use Yii;

/**
 * Busca las clases que implementen CountableInterface y BaseMovement
 *
 * Class ClassFinder
 * @package app\modules\accounting\components
 */
class ClassFinder
{
    private static $instance;

    /**
     * Retorna la instancia del singleton.
     *
     * @return ClassFinder
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ClassFinder();
        }
        return self::$instance;
    }

    /**
     * Busca en los directorios configuradios en el parametro "countable-implementation-dir"
     * las clases que implementan la interface CountableInterface.
     *
     * @return array
     */
    public function findCountables()
    {
        // Traigo el listado de directorios
        $config = $this->getConfiguration("countable-implementation-dir");;
        $classes = array();
        foreach (ClassFinderHelper::findClasses($config) as $clase) {
            $class = new ReflectionClass($clase);
            if (!$class->isAbstract() && !$class->isInterface() ) {
                if ($class->implementsInterface("app\\modules\\accounting\\components\\CountableInterface")) {
                    $classes[] = $clase;
                }
            }
        }
        return $classes;
    }

    /**
     * Busca en los directorios configuradios en el parametro "movement-implementation-dir"
     * las clases que extiendan de BaseMovement
     *
     * @return array
     */
    public function findMovements()
    {
        // Traigo el listado de directorios
        $config = $this->getConfiguration("movement-implementation-dir");
        $classes = array();
        foreach (ClassFinderHelper::findClasses($config) as $clase) {
            $class = new ReflectionClass($clase);
            if (!$class->isAbstract() && !$class->isInterface() ) {
                if ($class->getParentClass()->getName()=="app\\modules\\accounting\\components\\BaseMovement") {
                    $classes[] = $clase;
                }
            }
        }
        return $classes;
    }

    /**
     * Retorna la configuracion desde los archivos de parametros.
     *
     * @param $type
     * @return array
     */
    private function getConfiguration($type)
    {
        // Verifico si existe la configuracion
        if ( array_key_exists( 'accounting', Yii::$app->params ) ) {
            $accounting = Yii::$app->params['accounting'];
            // Verifico si existe la configuracion especifica
            if ( array_key_exists( $type, $accounting ) ) {
                return $accounting[$type];
            }
        }
        return array();
    }
}