<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 10/06/16
 * Time: 16:03
 */

namespace app\modules\westnet\mesa\components\models;


use yii\base\Model;

class Categoria extends Model {

    public $id;
    public $padre_id;
    public $nombre;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'padre_id'], 'integer' ],
            [['nombre'], 'string' ],
            [['id', 'nombre', 'padre_id'], 'safe' ],
        ];
    }

    public function __construct($categoria)
    {
        $this->id       = $categoria['id'];
        $this->padre_id = isset($categoria['padre_id']) ? $categoria['padre_id'] : null  ;
        $this->nombre   = $categoria['nombre'];
    }
}