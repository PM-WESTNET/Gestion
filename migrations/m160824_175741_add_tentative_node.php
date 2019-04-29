<?php

use yii\db\Migration;

class m160824_175741_add_tentative_node extends Migration
{
    public function up()
    {
            $this->execute('ALTER TABLE `contract` 
                    ADD COLUMN `tentative_node` INT(11) NULL DEFAULT NULL AFTER `external_id`;');
    }

    public function down()
    {
        echo "m160824_175741_add_tentative_node cannot be reverted.\n";

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
