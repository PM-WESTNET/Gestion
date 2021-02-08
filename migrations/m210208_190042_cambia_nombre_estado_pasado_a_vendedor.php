<?php

use app\modules\ticket\models\Status;
use yii\db\Migration;

/**
 * Class m210208_190042_cambia_nombre_estado_pasado_a_vendedor
 */
class m210208_190042_cambia_nombre_estado_pasado_a_vendedor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $status = Status::findOne([
            'name'=> 'Pasado al Vendedor'
        ]);
        if($status){
            $status->updateAttributes([
                'name'=>'Plazo Vencido'
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $status = Status::findOne([
            'name'=>'Plazo Vencido'
        ]);
        if($status){
            $status->updateAttributes([
                'name'=> 'Pasado al Vendedor'
            ]);
        }
    }

}
