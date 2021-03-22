<?php

use yii\db\Migration;

class m160930_130238_vendor_provider extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE vendor ADD provider_id INT NULL;');
        $this->execute('ALTER TABLE vendor  ADD CONSTRAINT vendor_provider_id_fk FOREIGN KEY (provider_id) REFERENCES provider (provider_id);');
    }

    public function down()
    {
        echo "m160930_130238_vendor_provider cannot be reverted.\n";

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
