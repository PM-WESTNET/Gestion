<?php

use yii\db\Migration;

class m160920_205533_contract_print_ads_attribute extends Migration
{
    public function up()
    {
        $this->execute(
                "ALTER TABLE `contract` 
                ADD COLUMN `print_ads` TINYINT(1) NULL DEFAULT 0 AFTER `tentative_node`");
    }

    public function down()
    {
        echo "m160920_205533_contract_print_ads_attribute cannot be reverted.\n";

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
