<?php

use yii\db\Migration;

class m180814_200205_mobile_push_has_user_app_change_schema extends Migration
{
    public function up()
    {
        $this->dropTable('mobile_push_has_user_app');
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `mobile_push_has_user_app` (
              `mobile_push_has_user_app_id` INT NOT NULL AUTO_INCREMENT,
              `user_app_id` INT NOT NULL,
              `mobile_push_id` INT NOT NULL,
              PRIMARY KEY (`mobile_push_has_user_app_id`),
              INDEX `fk_mobile_push_has_user_app_user_app2_idx` (`user_app_id` ASC),
              INDEX `fk_mobile_push_has_user_app_mobile_push2_idx` (`mobile_push_id` ASC),
              CONSTRAINT `fk_mobile_push_has_user_app_user_app2`
                FOREIGN KEY (`user_app_id`)
                REFERENCES `user_app` (`user_app_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_mobile_push_has_user_app_mobile_push2`
                FOREIGN KEY (`mobile_push_id`)
                REFERENCES `mobile_push` (`mobile_push_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB");
    }

    public function down()
    {
        $this->dropTable('mobile_push_has_user_app');

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `mobile_push_has_user_app` (
                  `mobile_push_id` INT NOT NULL,
                  `user_app_id` INT NOT NULL,
                  PRIMARY KEY (`mobile_push_id`, `user_app_id`),
                  INDEX `fk_mobile_push_has_user_app_user_app1_idx` (`user_app_id` ASC),
                  INDEX `fk_mobile_push_has_user_app_mobile_push1_idx` (`mobile_push_id` ASC),
                  CONSTRAINT `fk_mobile_push_has_user_app_mobile_push1`
                    FOREIGN KEY (`mobile_push_id`)
                    REFERENCES `mobile_push` (`mobile_push_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_mobile_push_has_user_app_user_app1`
                    FOREIGN KEY (`user_app_id`)
                    REFERENCES `user_app` (`user_app_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");
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
