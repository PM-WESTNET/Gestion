<?php

use yii\db\Migration;

class m161025_132523_customer_class_status extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `customer_class` 
                ADD COLUMN `status` ENUM('enabled', 'disabled') NULL DEFAULT 'enabled' AFTER `percentage_tolerance_debt`");
    }

    public function down()
    {
        echo "m161025_132523_customer_class_status cannot be reverted.\n";

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
