<?php

use yii\db\Migration;

class m180619_173117_destinatary_column_in_user_app extends Migration
{
    public function up()
    {
        $this->addColumn('user_app', 'destinatary', 'VARCHAR(255) NULL');
    }

    public function down()
    {
        $this->dropColumn('user_app', 'destinatary');
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
