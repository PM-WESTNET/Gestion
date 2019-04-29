<?php

use yii\db\Migration;

class m160920_175622_no_want_and_negative_survey_contract_status extends Migration
{
    public function up()
    {
        $this->execute(
                "ALTER TABLE `contract`
                CHANGE COLUMN `status` `status` ENUM('draft', 'active', 'inactive', 
                'canceled', 'low-process', 'low', 'no-want', 'negative-survey') NULL DEFAULT 'draft' ");
    }

    public function down()
    {
        echo "m160920_175622_no_want_and_negative_survey_contract_status cannot be reverted.\n";

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
