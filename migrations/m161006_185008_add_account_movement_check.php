<?php

use yii\db\Migration;

class m161006_185008_add_account_movement_check extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE account_movement ADD `check` BOOLEAN DEFAULT FALSE  NULL;');
        $this->execute('ALTER TABLE account_movement_item ADD `check` BOOLEAN DEFAULT FALSE  NULL;');
    }

    public function down()
    {
        echo "m161006_185008_add_account_movement_check cannot be reverted.\n";

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
