<?php

use yii\db\Migration;

class m160811_203235_customer_log_table extends Migration {

    public function up() {

        $this->execute("CREATE TABLE IF NOT EXISTS `customer_log` (
            `customer_log_id` INT(11) NOT NULL AUTO_INCREMENT,
            `action` VARCHAR(100) NOT NULL,
            `before_value` VARCHAR(45) NULL DEFAULT NULL,
            `new_value` VARCHAR(45) NULL DEFAULT NULL,
            `date` DATETIME NOT NULL,
            `customer_id` INT(11) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `observations` VARCHAR(300) NULL DEFAULT NULL,
            `object_id` INT(11) NOT NULL,
            `class_name` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`customer_log_id`),
            INDEX `fk_customer_log_customer1_idx` (`customer_id` ASC),
            CONSTRAINT `fk_customer_log_customer1`
              FOREIGN KEY (`customer_id`)
              REFERENCES `customer` (`customer_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB
          AUTO_INCREMENT = 11
          DEFAULT CHARACTER SET = utf8;");
        
        return true;
    }

    public function down() {
        $this->dropTable('customer_log');

        return true;
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
