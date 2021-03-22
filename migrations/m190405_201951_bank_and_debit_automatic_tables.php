<?php

use yii\db\Migration;

/**
 * Class m190405_201951_bank_and_debit_automati_tablesc
 */
class m190405_201951_bank_and_debit_automatic_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `bank` (
                  `bank_id` INT NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(45) NOT NULL,
                  `status` INT NOT NULL,
                  `class` VARCHAR(255) NOT NULL,
                  `created_at` INT NOT NULL,
                  `updated_at` INT NOT NULL,
                  PRIMARY KEY (`bank_id`))
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `automatic_debit` (
                  `automatic_debit_id` INT NOT NULL AUTO_INCREMENT,
                  `customer_id` INT(11) NOT NULL,
                  `bank_id` INT NOT NULL,
                  `cbu` VARCHAR(255) NOT NULL,
                  `beneficiario_number` VARCHAR(255) NOT NULL,
                  `status` INT NOT NULL,
                  `created_at` INT NOT NULL,
                  `updated_at` INT NOT NULL,
                  PRIMARY KEY (`automatic_debit_id`),
                  INDEX `fk_automatic_debit_customer1_idx` (`customer_id` ASC),
                  INDEX `fk_automatic_debit_bank1_idx` (`bank_id` ASC),
                  CONSTRAINT `fk_automatic_debit_customer1`
                    FOREIGN KEY (`customer_id`)
                    REFERENCES `customer` (`customer_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_automatic_debit_bank1`
                    FOREIGN KEY (`bank_id`)
                    REFERENCES `bank` (`bank_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");

        $this->execute(
            "CREATE TABLE IF NOT EXISTS `bank_company_config` (
                  `bank_company_config_id` INT NOT NULL AUTO_INCREMENT,
                  `company_identification` VARCHAR(45) NULL,
                  `branch` VARCHAR(45) NULL,
                  `control_digit` VARCHAR(45) NULL,
                  `account_number` VARCHAR(45) NULL,
                  `company_id` INT(11) NOT NULL,
                  `bank_id` INT NOT NULL,
                  `created_at` INT NOT NULL,
                  `updated_at` INT NOT NULL,
                  PRIMARY KEY (`bank_company_config_id`),
                  INDEX `fk_bank_company_config_company1_idx` (`company_id` ASC),
                  INDEX `fk_bank_company_config_bank1_idx` (`bank_id` ASC),
                  CONSTRAINT `fk_bank_company_config_company1`
                    FOREIGN KEY (`company_id`)
                    REFERENCES `company` (`company_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_bank_company_config_bank1`
                    FOREIGN KEY (`bank_id`)
                    REFERENCES `bank` (`bank_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bank_company_config');
        $this->dropTable('automatic_debit');
        $this->dropTable('bank');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190405_201951_bank_and_debit_automati_tablesc cannot be reverted.\n";

        return false;
    }
    */
}
