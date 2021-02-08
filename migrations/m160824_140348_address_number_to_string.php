<?php

use yii\db\Migration;

class m160824_140348_address_number_to_string extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `address` 
            CHANGE COLUMN `number` `number` VARCHAR(45) NULL DEFAULT NULL ;');
    }

    public function down()
    {
        echo "m160824_140348_address_number_to_string cannot be reverted.\n";

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
