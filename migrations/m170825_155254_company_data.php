<?php

use yii\db\Migration;

class m170825_155254_company_data extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE company ADD web VARCHAR(100) NULL;');
        $this->execute('ALTER TABLE company ADD portal_web VARCHAR(100) NULL;');
    }

    public function down()
    {
        echo "m170825_155254_company_data cannot be reverted.\n";

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
