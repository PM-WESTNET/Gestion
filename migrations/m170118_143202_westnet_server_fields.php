<?php

use yii\db\Migration;

class m170118_143202_westnet_server_fields extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE server ADD COLUMN user varchar(255) NULL DEFAULT ''" );
        $this->execute("ALTER TABLE server ADD COLUMN password varchar(255) NULL DEFAULT ''" );
        $this->execute("ALTER TABLE server ADD COLUMN class varchar(255) NULL DEFAULT ''" );
    }

    public function down()
    {
        echo "m170118_143202_westnet_server_fields cannot be reverted.\n";

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
