<?php

use yii\db\Migration;

class m161024_130002_customer_notification_way extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `customer` 
                ADD COLUMN `screen_notification` TINYINT(1) NULL DEFAULT 0 AFTER `publicity_shape`,
                ADD COLUMN `sms_notification` TINYINT(1) NULL DEFAULT 0 AFTER `screen_notification`,
                ADD COLUMN `email_notification` TINYINT(1) NULL DEFAULT 0 AFTER `sms_notification`,
                ADD COLUMN `sms_fields_notifications` VARCHAR(45) NULL DEFAULT NULL,
                ADD COLUMN `email_fields_notifications` VARCHAR(45) NULL DEFAULT NULL
        ");       
    }

    public function down()
    {
        echo "m161024_130002_customer_notification_way cannot be reverted.\n";

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
