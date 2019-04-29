<?php

use yii\db\Migration;

class m170529_115241_connection_clean extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE connection ADD COLUMN clean int  null DEFAULT 0');
        $this->execute('ALTER TABLE connection ADD COLUMN old_server_id int  null DEFAULT null');
    }

    public function down()
    {
        echo "m170529_115241_connection_clean cannot be reverted.\n";

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
