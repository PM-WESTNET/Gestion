<?php

use yii\db\Migration;

class m160722_204414_vendor_commission_table extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS `vendor_commission` (
            `vendor_commission_id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(45) NULL,
            `percentage` DOUBLE NULL,
            `value` DOUBLE NULL,
            PRIMARY KEY (`vendor_commission_id`))
          ENGINE = InnoDB");
        
        $this->execute("ALTER TABLE `vendor_liquidation_item` 
            ADD COLUMN `contract_detail_id` INT NULL,
            ADD COLUMN `description` VARCHAR(255) NULL,
            ADD CONSTRAINT `fk_vendor_liquidation_item_contract_detail1`
              FOREIGN KEY (`contract_detail_id`)
              REFERENCES `contract_detail` (`contract_detail_id`)");
        
        $this->execute("ALTER TABLE vendor ADD COLUMN `vendor_commission_id` INT NOT NULL;");
        
        $this->execute("ALTER TABLE `vendor_liquidation_item` 
            MODIFY COLUMN `bill_id` INT NULL,
            MODIFY COLUMN `contract_detail_id` INT NULL;");
        
        
    }

    public function down()
    {
        $this->dropForeignKey('fk_vendor_vendor_commission1', 'vendor');
        $this->dropColumn('vendor', 'vendor_commission_id');
        $this->dropTable('vendor_commission');
        
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
