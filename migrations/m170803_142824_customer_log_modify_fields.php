<?php

use yii\db\Migration;

class m170803_142824_customer_log_modify_fields extends Migration
{
    public function up()
    {
        $this->execute( "ALTER TABLE customer_log MODIFY before_value VARCHAR(450);" );
        $this->execute( "ALTER TABLE customer_log MODIFY new_value VARCHAR(450);");
    }

    public function down()
    {
        echo "m170803_142824_customer_log_modify_fields cannot be reverted.\n";

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
