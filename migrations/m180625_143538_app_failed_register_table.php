<?php

use yii\db\Migration;

class m180625_143538_app_failed_register_table extends Migration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `app_failed_register` (
                  `app_failed_register_id` INT NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(45) NOT NULL,
                  `lastname` VARCHAR(45) NOT NULL,
                  `document_type` VARCHAR(45) NOT NULL,
                  `document_number` VARCHAR(45) NOT NULL,
                  `customer_code` INT NOT NULL,
                  `email` VARCHAR(255) NOT NULL,
                  `phone` VARCHAR(45) NOT NULL,
                  `status` ENUM('pending', 'closed') NOT NULL,
                  PRIMARY KEY (`app_failed_register_id`))
                ENGINE = InnoDB");
    }

    public function down()
    {
        $this->dropTable('app_failed_register');
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
