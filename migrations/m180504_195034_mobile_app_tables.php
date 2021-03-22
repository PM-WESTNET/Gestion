<?php

use yii\db\Migration;

class m180504_195034_mobile_app_tables extends Migration
{
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `user_app` (
                  `user_app_id` INT NOT NULL AUTO_INCREMENT,
                  `email` VARCHAR(45) NULL,
                  `password_hash` VARCHAR(255) NULL,
                  `facebook_id` VARCHAR(255) NULL,
                  `google_id` VARCHAR(255) NULL,
                  `status` ENUM('pending', 'active', 'disable') NOT NULL DEFAULT 'pending',
                  PRIMARY KEY (`user_app_id`))
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `auth_token` (
                  `auth_token_id` INT NOT NULL AUTO_INCREMENT,
                  `token` VARCHAR(255) NOT NULL,
                  `expire_timestamp` VARCHAR(45) NOT NULL,
                  `user_app_id` INT NOT NULL,
                  PRIMARY KEY (`auth_token_id`),
                  INDEX `fk_auth_token_user_app1_idx` (`user_app_id` ASC),
                  CONSTRAINT `fk_auth_token_user_app1`
                    FOREIGN KEY (`user_app_id`)
                    REFERENCES `user_app` (`user_app_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `user_app_has_customer` (
                  `user_app_has_customer_id` INT NOT NULL AUTO_INCREMENT,
                  `user_app_id` INT NOT NULL,
                  `customer_id` INT NULL,
                  `customer_code` BIGINT NULL,
                  INDEX `fk_user_app_has_customer_customer1_idx` (`customer_id` ASC),
                  INDEX `fk_user_app_has_customer_user_app1_idx` (`user_app_id` ASC),
                  PRIMARY KEY (`user_app_has_customer_id`),
                  CONSTRAINT `fk_user_app_has_customer_user_app1`
                    FOREIGN KEY (`user_app_id`)
                    REFERENCES `user_app` (`user_app_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_user_app_has_customer_customer1`
                    FOREIGN KEY (`customer_id`)
                    REFERENCES `customer` (`customer_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `validation_code` (
                  `validation_code_id` INT NOT NULL AUTO_INCREMENT,
                  `code` VARCHAR(255) NOT NULL,
                  `expire_timestamp` INT NOT NULL,
                  `user_app_has_customer_id` INT NOT NULL,
                  PRIMARY KEY (`validation_code_id`),
                  INDEX `fk_validation_code_user_app_has_customer1_idx` (`user_app_has_customer_id` ASC),
                  CONSTRAINT `fk_validation_code_user_app_has_customer1`
                    FOREIGN KEY (`user_app_has_customer_id`)
                    REFERENCES `user_app_has_customer` (`user_app_has_customer_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");

    }

    public function safeDown()
    {
        $this->dropTable('validation_code');
        $this->dropTable('user_app_has_customer');
        $this->dropTable('auth_token');
        $this->dropTable('user_app');
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
