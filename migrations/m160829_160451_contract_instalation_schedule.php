<?php

use yii\db\Migration;

class m160829_160451_contract_instalation_schedule extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `contract` 
                ADD COLUMN `instalation_schedule` ENUM('in the morning', 'in the afternoon') NULL DEFAULT NULL AFTER `tentative_node`
            ");
    }

    public function down()
    {
        echo "m160829_160451_contract_instalation_schedule cannot be reverted.\n";

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
