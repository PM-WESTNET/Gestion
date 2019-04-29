<?php

use yii\db\Migration;

class m160801_221952_set_contract_detail_count_to_1 extends Migration
{
    public function up()
    {
        $this->execute("UPDATE `contract_detail` SET `count` = 1 WHERE `count` = 0 or `count` IS NULL");

    }

    public function down()
    {
        echo "m160801_221952_set_contract_detail_count_to_1 cannot be reverted.\n";

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
