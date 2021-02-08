<?php

use yii\db\Migration;

class m161013_191754_vendor_liquidation_billed_status extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE vendor_liquidation MODIFY status ENUM('draft', 'payed', 'cancelled', 'billed')");
    }

    public function down()
    {
        echo "m161013_191754_vendor_liquidation_billed_status cannot be reverted.\n";

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
