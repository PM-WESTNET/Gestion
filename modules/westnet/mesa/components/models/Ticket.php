<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 16:03
 */

namespace app\modules\westnet\mesa\components\models;


use yii\base\Model;

class Ticket extends Model {

    public $id;
    public $asignado;
    public $autor;
    public $categoria;
    public $estado;
    public $fecha_actualizacion;
    public $fecha_alta;
    public $fecha_cierre;
    public $historial;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer' ],
            [['estado'], 'string' ],
            [['fecha_actualizacion', 'fecha_alta', 'fecha_cierre'], 'datetime' ],
            [['id', 'nombre', 'padre_id'], 'safe' ],
        ];
    }

    public function __construct($id,$ticket)
    {
        $this->id                   = $id;
        $this->asignado             = new Usuario($ticket['asignado']);
        $this->autor                = new Usuario($ticket['autor']);
        $this->categoria            = new Categoria($ticket['categoria']);
        $this->estado               = $ticket['estado'];
        $this->fecha_alta           = new \DateTime($ticket['fecha_alta']);
        $this->fecha_actualizacion  = new \DateTime($ticket['fecha_actualizacion']);
        $this->fecha_alta           = new \DateTime($ticket['fecha_alta']);
        if($ticket['historial']) {
            foreach ($ticket['historial'] as $historial) {
                $this->historial[] = new HistoricoTicket($historial);
            }
        }
    }
}