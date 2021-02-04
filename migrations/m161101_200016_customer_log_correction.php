<?php

use yii\db\Migration;

class m161101_200016_customer_log_correction extends Migration
{
    public function up()
    {
        $this->update('customer_log', ['before_value' => 'Habilitado'], ['before_value' => 'enabled']);
        $this->update('customer_log', ['before_value' => 'Cortado'], ['before_value' => 'clipped']);
        $this->update('customer_log', ['before_value' => 'Moroso'], ['before_value' => 'defaulter']);
        $this->update('customer_log', ['before_value' => 'Forzado'], ['before_value' => 'forced']);
        $this->update('customer_log', ['new_value' => 'Habilitado'], ['new_value' => 'enabled']);
        $this->update('customer_log', ['new_value' => 'Cortado'], ['new_value' => 'clipped']);
        $this->update('customer_log', ['new_value' => 'Moroso'], ['new_value' => 'defaulter']);
        $this->update('customer_log', ['new_value' => 'Forzado'], ['new_value' => 'forced']);
        
        $change_ip= $this->db->createCommand("SELECT * FROM customer_log WHERE action='Actualizacion de Datos de Conexion: Ip4 1'")->queryAll();
        
        foreach ($change_ip as $cip){
            $this->update('customer_log', ['before_value' => long2ip($cip['before_value']), 'new_value' => long2ip($cip['new_value'])], ['customer_log_id' => $cip['customer_log_id']]);
        }
    }

    public function down()
    {
        echo "m161101_200016_customer_log_correction cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
