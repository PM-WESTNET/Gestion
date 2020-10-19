<?php

use yii\db\Migration;

/**
 * Class m201019_190930_firstdata_tables
 */
class m201019_190930_firstdata_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_company_config` (
            `firstdata_company_config_id` INT NOT NULL AUTO_INCREMENT,
            `commerce_number` INT NOT NULL,
            `company_id` INT(11) NOT NULL,
            PRIMARY KEY (`firstdata_company_config_id`),
            INDEX `fk_firstdata_company_config_company1_idx` (`company_id` ASC),
            CONSTRAINT `fk_firstdata_company_config_company1`
              FOREIGN KEY (`company_id`)
              REFERENCES `company` (`company_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB");

        $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_export` (
            `firstdata_export_id` INT NOT NULL AUTO_INCREMENT,
            `created_at` INT NOT NULL,
            `file_url` VARCHAR(255) NOT NULL,
            `firstdata_config_id` INT NOT NULL,
            PRIMARY KEY (`firstdata_export_id`),
            INDEX `fk_firstdata_export_firstdata_company_config1_idx` (`firstdata_config_id` ASC),
            CONSTRAINT `fk_firstdata_export_firstdata_company_config1`
              FOREIGN KEY (`firstdata_config_id`)
              REFERENCES `firstdata_company_config` (`firstdata_company_config_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB");

          $this->execute("CREATE TABLE IF NOT EXISTS `bill_has_firstdata_export` (
            `bill_has_firstdata_export_id` INT NOT NULL AUTO_INCREMENT,
            `bill_id` INT(11) NOT NULL,
            `firstdata_export_id` INT NOT NULL,
            INDEX `fk_bill_has_firstdata_export_firstdata_export1_idx` (`firstdata_export_id` ASC),
            INDEX `fk_bill_has_firstdata_export_bill1_idx` (`bill_id` ASC),
            PRIMARY KEY (`bill_has_firstdata_export_id`),
            CONSTRAINT `fk_bill_has_firstdata_export_bill1`
              FOREIGN KEY (`bill_id`)
              REFERENCES `bill` (`bill_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_bill_has_firstdata_export_firstdata_export1`
              FOREIGN KEY (`firstdata_export_id`)
              REFERENCES `firstdata_export` (`firstdata_export_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB
          DEFAULT CHARACTER SET = utf8");

       
          
          $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_automatic_debit` (
            `firstdata_automatic_debit_id` INT NOT NULL AUTO_INCREMENT,
            `customer_id` INT(11) NOT NULL,
            `company_config_id` INT NOT NULL,
            PRIMARY KEY (`firstdata_automatic_debit_id`),
            INDEX `fk_firstdata_automatic_debit_customer1_idx` (`customer_id` ASC),
            INDEX `fk_firstdata_automatic_debit_firstdata_company_config1_idx` (`company_config_id` ASC),
            CONSTRAINT `fk_firstdata_automatic_debit_customer1`
              FOREIGN KEY (`customer_id`)
              REFERENCES `customer` (`customer_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_firstdata_automatic_debit_firstdata_company_config1`
              FOREIGN KEY (`company_config_id`)
              REFERENCES `firstdata_company_config` (`firstdata_company_config_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB");
         
         $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_debit_has_export` (
            `firstdata_debit_has_export_id` INT NOT NULL AUTO_INCREMENT,
            `firstdata_automatic_debit_id` INT NOT NULL,
            `firstdata_export_id` INT NOT NULL,
            INDEX `fk_firstdata_automatic_debit_has_firstdata_export_firstdata_idx` (`firstdata_export_id` ASC),
            INDEX `fk_firstdata_automatic_debit_has_firstdata_export_firstdata_idx1` (`firstdata_automatic_debit_id` ASC),
            PRIMARY KEY (`firstdata_debit_has_export_id`),
            CONSTRAINT `fk_firstdata_automatic_debit_has_firstdata_export_firstdata_a1`
              FOREIGN KEY (`firstdata_automatic_debit_id`)
              REFERENCES `firstdata_automatic_debit` (`firstdata_automatic_debit_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_firstdata_automatic_debit_has_firstdata_export_firstdata_e1`
              FOREIGN KEY (`firstdata_export_id`)
              REFERENCES `firstdata_export` (`firstdata_export_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB"); 

        

        $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_import` (
            `firstdata_import_id` INT NOT NULL AUTO_INCREMENT,
            `presentation_date` INT NOT NULL,
            `created_at` INT NOT NULL,
            `status` ENUM('draft', 'success', 'error') NOT NULL,
            `response_file` VARCHAR(255) NOT NULL,
            `observation_file` VARCHAR(255) NULL,
            PRIMARY KEY (`firstdata_import_id`))
          ENGINE = InnoDB");

        $this->execute("CREATE TABLE IF NOT EXISTS `firstdata_import_payment` (
            `firstdata_import_payment_id` INT NOT NULL,
            `firstdata_import_id` INT NOT NULL,
            `customer_code` INT NOT NULL,
            `customer_id` INT(11) NULL,
            `payment_id` INT(11) NULL,
            `status` ENUM('pending', 'success', 'error') NOT NULL,
            PRIMARY KEY (`firstdata_import_payment_id`),
            INDEX `fk_firstdata_import_payment_firstdata_import1_idx` (`firstdata_import_id` ASC),
            INDEX `fk_firstdata_import_payment_customer1_idx` (`customer_id` ASC),
            INDEX `fk_firstdata_import_payment_payment1_idx` (`payment_id` ASC),
            CONSTRAINT `fk_firstdata_import_payment_firstdata_import1`
              FOREIGN KEY (`firstdata_import_id`)
              REFERENCES `firstdata_import` (`firstdata_import_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_firstdata_import_payment_customer1`
              FOREIGN KEY (`customer_id`)
              REFERENCES `customer` (`customer_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_firstdata_import_payment_payment1`
              FOREIGN KEY (`payment_id`)
              REFERENCES `payment` (`payment_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION)
          ENGINE = InnoDB");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('firstdata_debit_has_export');
        $this->dropTable('firstdata_automatic_debit');
        $this->dropTable('bill_has_firstdata_export_');
        $this->dropTable('firstdata_export');
        $this->dropTable('firstdata_company_config');
        $this->dropTable('firstdata_import');
        $this->dropTable('firstdata_import_payment');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_190930_firstdata_tables cannot be reverted.\n";

        return false;
    }
    */
}
