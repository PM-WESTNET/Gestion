<?php

use yii\db\Migration;

class m180816_181119_app_failed_register_remove_fields extends Migration
{
    public function up()
    {
        $this->dropColumn('app_failed_register', 'lastname');
        $this->dropColumn('app_failed_register', 'customer_code');
    }

    public function down()
    {
       $this->addColumn('app_failed_register', 'lastname', 'VARCHAR(45) NULL');
       $this->addColumn('app_failed_register', 'customer_code', 'INT NULL');
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
