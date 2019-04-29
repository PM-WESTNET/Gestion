<?php

use yii\db\Migration;

class m180814_192805_mobile_push_content_to_text extends Migration
{
    public function up()
    {
        $this->alterColumn('mobile_push', 'content', 'TEXT NULL');
    }

    public function down()
    {
        $this->alterColumn('mobile_push', 'content', 'VARCHAR(45) NULL');
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
