<?php

use yii\db\Migration;

/**
 * Class m210712_195335_insert_payment_method_boton_de_pago_into_payment_method_table
 */
class m210712_195335_insert_payment_method_boton_de_pago_into_payment_method_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payment_method', [
            'name' => 'Botón de Pago',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => null,
            'send_ivr' => 0,
            'show_in_app' => 0
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210712_195335_insert_payment_method_boton_de_pago_into_payment_method_table cannot be reverted.\n";

        return false;
    }
    */
}
