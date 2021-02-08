<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 16:03
 */

namespace app\modules\westnet\mesa\components\models;


use yii\base\Model;

class Usuario extends Model {

    public $id;
    public $nombre;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer' ],
            [['nombre'], 'string' ],
            [['id', 'nombre'], 'safe' ],
        ];
    }

    public function __construct($usuario)
    {
        $this->id       = $usuario['id'];
        $this->nombre   = $usuario['nombre'];
    }
}