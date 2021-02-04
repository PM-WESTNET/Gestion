<?php

use yii\db\Migration;

class m160830_134402_connection_forced_history_table extends Migration
{
    public function up()
    {
        $this->execute(
                "CREATE TABLE IF NOT EXISTS `connection_forced_historial` (
                    `connection_forced_historial_id` INT NOT NULL AUTO_INCREMENT,
                    `date` DATE NOT NULL,
                    `reason` VARCHAR(500) NULL,
                    `connection_id` INT NOT NULL,
                    `user_id` INT NOT NULL,
                PRIMARY KEY (`connection_forced_historial_id`),
                    INDEX `fk_connection_forced_historial_connection1_idx` (`connection_id` ASC),
                    CONSTRAINT `fk_connection_forced_historial_connection1`
                    FOREIGN KEY (`connection_id`)
                    REFERENCES `connection` (`connection_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
                ENGINE = InnoDB");
    }

    public function down()
    {
        echo "m160830_134402_connection_forced_historial_table cannot be reverted.\n";

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
