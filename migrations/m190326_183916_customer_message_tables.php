<?php

use yii\db\Migration;

/**
 * Class m190326_183916_customer_message_tables
 */
class m190326_183916_customer_message_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `customer_message` (
                  `customer_message_id` INT NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(45) NOT NULL,
                  `message` TEXT NOT NULL,
                  `status` INT NOT NULL,
                  PRIMARY KEY (`customer_message_id`))
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `customer_has_customer_message` (
                  `chcm_id` INT(11) NOT NULL AUTO_INCREMENT,
                  `customer_id` INT(11) NOT NULL,
                  `customer_message_id` INT NOT NULL,
                  `timestamp` INT NOT NULL,
                  INDEX `fk_customer_has_customer_message_customer_message1_idx` (`customer_message_id` ASC),
                  INDEX `fk_customer_has_customer_message_customer1_idx` (`customer_id` ASC),
                  PRIMARY KEY (`chcm_id`),
                  CONSTRAINT `fk_customer_has_customer_message_customer1`
                    FOREIGN KEY (`customer_id`)
                    REFERENCES `customer` (`customer_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_customer_has_customer_message_customer_message1`
                    FOREIGN KEY (`customer_message_id`)
                    REFERENCES `customer_message` (`customer_message_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('customer_has_customer_message');
       $this->dropTable('customer_message');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_183916_customer_message_tables cannot be reverted.\n";

        return false;
    }
    */
}
