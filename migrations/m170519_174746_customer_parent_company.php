<?php

use yii\db\Migration;

class m170519_174746_customer_parent_company extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE customer ADD COLUMN parent_company_id int NULL DEFAULT NULL" );
        $this->execute("CREATE INDEX ix_customer_parent_customer_id ON customer (parent_company_id);");

    }

    public function down()
    {
        echo "m170519_174746_customer_parent_company cannot be reverted.\n";

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
