<?php

use yii\db\Migration;

class m160913_140942_contract_and_connection_new_status extends Migration
{
    public function up()
    {
        $this->execute(
                "ALTER TABLE `contract`
                 CHANGE COLUMN `status` `status` ENUM('draft', 'active', 'inactive', 'canceled', 'low-process', 'low') NULL DEFAULT 'draft'"
                );
        
        $this->execute(
                "ALTER TABLE `connection`
                 CHANGE COLUMN `status` `status` ENUM('enabled', 'disabled', 'forced', 'low') NOT NULL DEFAULT 'disabled' ,
                 CHANGE COLUMN `status_account` `status_account` ENUM('enabled', 'disabled', 'forced', 'defaulter', 'clipped', 'low') NULL DEFAULT NULL"
                );

    }

    public function down()
    {
        echo "m160913_140942_contract_and_connection_new_status cannot be reverted.\n";

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
