<?php

namespace app\modules\westnet\mesa\components\models;

use Yii;
use yii\base\Model;

/**
 * Description of Notificacion
 *
 * @author mmoyano
 */
class Notificacion extends Model
{
    
    public $id;
    public $titulo;
    public $texto;
    public $base_url;
    public $desde;
    public $hasta;
    public $horas;
    public $ips;
    public $filtros;
    
    public function rules()
    {
        return [
            [['id', 'titulo', 'texto', 'base_url', 'desde', 'hasta', 'horas', 'ips'], 'required'],
            [['id', 'titulo', 'texto', 'base_url'], 'string'],
            [['desde', 'hasta'], 'date', 'format' => 'yyyy-MM-dd'],
            [['horas'], 'each', 'rule' => ['date', 'format' => 'hh:ii:ss']],
            [['ips'], 'each', 'rule' => ['ip']],
            [['filtros'], 'safe']
        ];
    }
    
    public function fields()
    {
        return [
            'id',
            'titulo',
            'texto',
            'base_url',
            'desde',
            'hasta',
            'horas',
            'ips',
            'filtros'
        ];
    }
    
}
