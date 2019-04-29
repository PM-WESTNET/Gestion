<?php

use yii\db\Migration;

class m160920_152151_contract_installation_schedule_all_day extends Migration
{
    public function up()
    {
        $this->execute(
                "ALTER TABLE `contract` 
                CHANGE COLUMN `instalation_schedule` `instalation_schedule` ENUM('in the morning', 'in the afternoon', 'all day') 
                NULL DEFAULT NULL");
    }

    public function down()
    {
        echo "m160920_152151_contract_installation_schedule_all_day cannot be reverted.\n";

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
