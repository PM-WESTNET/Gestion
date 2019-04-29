<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/08/17
 * Time: 11:18
 */

namespace app\modules\mailing\services;


use app\modules\mailing\models\EmailTransport;
use app\modules\mailing\models\search\EmailTransportSearch;

class ConfigMailing
{

    /**
     * Retorna la configuración de envio, se busca solamente en la base de datos.
     * Solo se retorna un valor.
     * Las busquedas pueden ser:
     *  - por Nombre
     *  - por relacion y id
     *  - por nombre, relacion y id
     *
     * @param null $name
     * @param null $relation_class
     * @param null $relation_id
     * @return EmailTransport | null
     */
    public static function getConfig($name=null, $relation_class = null, $relation_id = null)
    {
        if($name === null && $relation_class === null &&  $relation_id === null) {
            return null;
        }

        $search = new EmailTransportSearch();
        return $search->findBy($name, $relation_class, $relation_id);

    }

    /**
     * Retorno los transportes definidos en el archivo de configuracion.
     *
     * @return array
     */
    public static function getTransports()
    {
        $config = \Yii::$app->params['mailing'];

        if(array_key_exists('transports', $config)) {
            return $config['transports'];
        }
    }

    /**
     * Retorno los layouts definidos en los archivos de configuración
     *
     * @return array
     */
    public static function getLayouts()
    {
        $config = \Yii::$app->params['mailing'];

        if(array_key_exists('layouts', $config)) {
            return $config['layouts'];
        }
    }

    /**
     * Retorno las clases que se pueden relacionar con el transporte
     *
     * @return array
     */
    public static function getRelationClases()
    {
        $config = \Yii::$app->params['mailing'];

        if(array_key_exists('relation_clases', $config)) {
            return $config['relation_clases'];
        }
    }
}