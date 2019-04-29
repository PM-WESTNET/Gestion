<?php

use yii\db\Migration;

class m180508_131844_auth_token_status_column_expire_timestamp_to extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('auth_token', 'expire_timestamp', 'INT NOT NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('auth_token', 'expire_timestamp', 'VARCHAR(45) NOT NULL');
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
