<?php

use yii\db\Migration;

class m171113_131806_discount_description extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE customer_has_discount ADD description VARCHAR(100) NULL;');
    }

    public function down()
    {
        echo "m171113_131806_discount_description cannot be reverted.\n";

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
