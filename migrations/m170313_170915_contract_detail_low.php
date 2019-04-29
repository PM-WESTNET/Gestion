<?php

use yii\db\Migration;

class m170313_170915_contract_detail_low extends Migration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE contract_detail CHANGE COLUMN status status ENUM('draft', 'active', 'canceled', 'low', 'low-process') NOT NULL DEFAULT 'draft';"
        );
    }

    public function down()
    {
        echo "m170313_170915_contract_detail_low cannot be reverted.\n";

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
