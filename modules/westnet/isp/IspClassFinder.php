<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/08/15
 * Time: 16:51
 */

namespace app\modules\westnet\isp;

use app\components\helpers\ClassFinderHelper;
use ReflectionClass;
use Yii;

/**
 * Busca las clases que implementen IspFactory
 *
 * Class IspClassFinder
 * @package app\modules\westnet\isp
 */
class IspClassFinder
{
    private static $instance;

    /**
     * Retorna la instancia del singleton.
     *
     * @return IspClassFinder
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new IspClassFinder();
        }
        return self::$instance;
    }

    /**
     * Busca en los directorios configuradios en el parametro "movement-implementation-dir"
     * las clases que extiendan de BaseMovement
     *
     * @return array
     */
    public function findIsp()
    {
        // Traigo el listado de directorios
        $config = $this->getConfiguration();
        $classes = array();
        foreach (ClassFinderHelper::findClasses($config) as $clase) {
            $class = new ReflectionClass($clase);
            if( $class->implementsInterface('app\\modules\\westnet\\isp\\IspInterface') ) {
                $classes[] = $clase;
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
    private function getConfiguration()
    {
        // Verifico si existe la configuracion
        if ( array_key_exists( 'isp', Yii::$app->params ) ) {
            return Yii::$app->params['isp'];
        }
        return array();
    }
}