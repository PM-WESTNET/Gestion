<?php

use yii\db\Migration;

class m160915_124740_pago_facil_payment_method extends Migration
{
    public function up()
    {
        $this->insert('payment_method', 
            [
                'name'=> 'Pago Facil',
                'status' => 'enabled',
                'register_number'=> true,
                'type' => 'exchanging',
            ]);

    }

    public function down()
    {
        echo "m160915_124740_pago_facil_payment_method cannot be reverted.\n";

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
