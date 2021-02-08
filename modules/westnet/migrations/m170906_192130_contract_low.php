<?php

use yii\db\Migration;

class m170906_192130_contract_low extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE contract ADD low_date date null;');
        $this->execute('ALTER TABLE contract ADD category_low_id int null;');
    }

    public function down()
    {
        echo "m170906_192130_contract_low cannot be reverted.\n";

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
