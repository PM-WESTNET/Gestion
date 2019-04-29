<?php

use yii\db\Migration;

class m161024_182210_set_notifications_way extends Migration
{
    public function up()
    {
        $this->execute('UPDATE customer set screen_notification= 1, sms_notification= 1, email_notification= 1, sms_fields_notifications= "phone,phone2,phone3", email_fields_notifications= "email,email2"');
    }

    public function down()
    {
        echo "m161024_182210_set_notifications_way cannot be reverted.\n";

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
