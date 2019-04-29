<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 16:03
 */

namespace app\modules\westnet\mesa\components\models;


use yii\base\Model;

class HistoricoTicket extends Model {
    public $id;
    public $asignado;
    public $autor;
    public $categoria;
    public $estado;
    public $descripcion;
    public $fecha_actualizacion;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'padre_id'], 'integer' ],
            [['estado'], 'string' ],
            [['fecha_actualizacion', 'fecha_alta', 'fecha_cierre'], 'datetime' ],
            [['id', 'nombre', 'padre_id'], 'safe' ],
        ];
    }

    public function __construct($historico)
    {
        $this->asignado             = new Usuario($historico['asignado']);
        if(array_key_exists('autor', $historico)!==false) {
            $this->autor                = new Usuario($historico['autor']);
        }
        $this->categoria            = new Categoria($historico['categoria']);
        $this->estado               = $historico['estado'];
        $this->descripcion          = $historico['descripcion'];
        $this->fecha_actualizacion  = new \DateTime($historico['fecha_actualizacion']);
    }
}
