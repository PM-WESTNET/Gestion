<?php

use yii\db\Migration;

class m180815_183926_customer_code_not_required_in_app_failed_register extends Migration
{
    public function up()
    {
        $this->alterColumn('app_failed_register', 'customer_code', 'INT NULL');
    }

    public function down()
    {
        $this->alterColumn('app_failed_register', 'customer_code', 'INT NOT NULL');
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
