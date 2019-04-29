<?php

use yii\db\Migration;

class m160819_190943_low_process_contract_status extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE .`contract`
                CHANGE COLUMN `status` `status` ENUM('draft', 'active', 'inactive', 'canceled', 'low-process') NULL DEFAULT 'draft' ");
    }

    public function down()
    {
        echo "m160819_190943_low_process_contract_status cannot be reverted.\n";

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
