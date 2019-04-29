<?php

use yii\db\Migration;

class m160729_002417_product_commission extends Migration
{
    public function up()
    {
        
        $this->execute("CREATE TABLE IF NOT EXISTS `product_commission` (
            `product_commission_id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(45) NULL,
            `percentage` DOUBLE NULL,
            `value` DOUBLE NULL,
            PRIMARY KEY (`product_commission_id`))
          ENGINE = InnoDB");
        
        $this->execute("ALTER TABLE `product` 
            ADD COLUMN `product_commission_id` INT(11) NULL DEFAULT NULL AFTER `account_id`,
            ADD INDEX `fk_product_product_commission1_idx` (`product_commission_id` ASC);");
        
        $this->execute("ALTER TABLE `product` 
            ADD CONSTRAINT `fk_product_product_commission1`
              FOREIGN KEY (`product_commission_id`)
              REFERENCES `product_commission` (`product_commission_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;");
        
    }

    public function down()
    {
        echo "m160729_002417_product_commission cannot be reverted.\n";

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
