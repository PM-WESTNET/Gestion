<?php

use yii\db\Migration;

class m161005_184155_account_movement_broken extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE account_movement MODIFY status ENUM('draft', 'closed', 'broken') NOT NULL DEFAULT 'draft'");
    }

    public function down()
    {
        echo "m161005_184155_account_movement_broken cannot be reverted.\n";

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
