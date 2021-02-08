<?php

use yii\base\Exception;
use yii\db\Migration;
use yii\db\Query;

class m160922_130633_notifications_filters extends Migration
{
    public function init() {
        $this->db= 'dbnotifications';
        parent::init();
        
    }
    public function up()
    {
        $transaction= $this->db->beginTransaction();
        
            
            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_company` (
                    `company_id` INT NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`company_id`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_company_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_company_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8");

            

            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_customer_status` (
                    `customer_status` VARCHAR(45) NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`customer_status`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_customer_status_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_customer_status_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8" );

            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_customer_category` (
                    `customer_category_id` INT NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`customer_category_id`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_customer_category_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_customer_category_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8");

            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_customer_class` (
                    `customer_class_id` INT NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`customer_class_id`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_customer_class_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_customer_class_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8");

            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_contract_status` (
                    `contract_status` VARCHAR(45) NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`contract_status`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_contract_status_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_contract_status_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8");

            $this->execute(
                    "CREATE TABLE IF NOT EXISTS `destinatary_has_plan` (
                    `plan_id` INT NOT NULL,
                    `destinatary_destinatary_id` INT NOT NULL,
                PRIMARY KEY (`plan_id`, `destinatary_destinatary_id`),
                INDEX `fk_destinatary_has_plan_destinatary1_idx` (`destinatary_destinatary_id` ASC),
                CONSTRAINT `fk_destinatary_has_plan_destinatary1`
                    FOREIGN KEY (`destinatary_destinatary_id`)
                    REFERENCES `destinatary` (`destinatary_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB DEFAULT CHARSET= UTF8");

            
            
            $old_destinatary= $this->db->createCommand("SELECT * FROM destinatary")->queryAll();
            
            foreach ($old_destinatary as $destinatary){
                if(!empty($destinatary['company_id'])){
                    $this->insert('destinatary_has_company', ['destinatary_destinatary_id' => $destinatary['destinatary_id'], 'company_id' => $destinatary['company_id']]);
                }
                if(!empty($destinatary['customer_class_id'])){
                    $this->insert('destinatary_has_customer_class', ['destinatary_destinatary_id' => $destinatary['destinatary_id'], 'customer_class_id' => $destinatary['customer_class_id']]);
                }
                if(!empty($destinatary['plan_id'])){
                    $this->insert('destinatary_has_plan', ['destinatary_destinatary_id' => $destinatary['destinatary_id'], 'plan_id' => $destinatary['plan_id']]);
                }
                if(!empty($destinatary['contract_status'])){
                    $this->insert('destinatary_has_contract_status', ['destinatary_destinatary_id' => $destinatary['destinatary_id'], 'contract_status' => $destinatary['contract_status']]);
                }
                if(!empty($destinatary['customer_id'])){
                    $this->insert('destinatary_has_customer_status', ['destinatary_destinatary_id' => $destinatary['destinatary_id'], 'customer_status' => $destinatary['customer_status']]);
                }
            }
            
            $this->execute(
                    "ALTER TABLE `destinatary` 
                    DROP COLUMN `contract_status`,
                    DROP COLUMN `plan_id`,
                    DROP COLUMN `customer_class_id`,
                    DROP COLUMN `customer_status`,
                    DROP COLUMN `company_id`");
            
            $transaction->commit();
        
    }

    public function down()
    {
        echo "m160922_130633_notifications_filters cannot be reverted.\n";

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
