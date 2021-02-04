<?php

use yii\db\Migration;

class m180806_184547_lastname_not_required_in_app_failed_register extends Migration
{
    public function up()
    {
        $this->alterColumn('app_failed_register', 'lastname', 'VARCHAR(45) NULL');
    }

    public function down()
    {
        $this->alterColumn('app_failed_register', 'lastname', 'VARCHAR(45) NOT NULL DEFAULT "-"');
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
