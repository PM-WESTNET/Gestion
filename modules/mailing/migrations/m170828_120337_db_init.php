<?php

use yii\db\Migration;

class m170828_120337_db_init extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE email_transport(
            email_transport_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(50)  NOT NULL,
            from_email varchar(50) NOT NULL,
            transport VARCHAR(100) NOT NULL,
            host VARCHAR(50)  NOT NULL,
            port INT  NOT NULL,
            username VARCHAR(50)  NOT NULL,
            password VARCHAR(50)  NOT NULL,
            encryption VARCHAR(10),
            layout varchar(100),
            relation_class varchar(100),
            relation_id int
        );' );
    }

    public function down()
    {
        echo "m170828_120337_db_init cannot be reverted.\n";

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
