<?php

use yii\db\Migration;

class m171106_153824_update_connections extends Migration
{
    public function up()
    {
        $this->execute('update connection LEFT JOIN contract con on connection.contract_id = con.contract_id
                        LEFT JOIN customer cus on con.customer_id = cus.customer_id
                        set connection.company_id = cus.company_id;');
    }

    public function down()
    {
        echo "m171106_153824_update_connections cannot be reverted.\n";

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
