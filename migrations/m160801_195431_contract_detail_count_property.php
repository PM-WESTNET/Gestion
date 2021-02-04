<?php

use yii\db\Migration;

class m160801_195431_contract_detail_count_property extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `contract_detail` ADD COLUMN `count` FLOAT NOT NULL DEFAULT 0 AFTER `discount_id`;");
    }

    public function down()
    {
        echo "m160801_195431_contract_detail_count_property cannot be reverted.\n";

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
