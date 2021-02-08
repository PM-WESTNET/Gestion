<?php

use yii\db\Migration;

class m180619_143155_document_number_column_in_user_app extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user_app', 'document_number', 'VARCHAR(45) NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('user_app', 'document_number');
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
