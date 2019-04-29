<?php

use yii\db\Migration;

class m160721_194639_contract_detail_vendor_id extends Migration
{
    public function up()
    {
        
        $sql = "
        ALTER TABLE `contract_detail` 
            ADD COLUMN `vendor_id` INT(11) NULL,
            ADD INDEX `fk_contract_detail_vendor1_idx` (`vendor_id` ASC),
            ADD CONSTRAINT `fk_contract_detail_vendor1`
              FOREIGN KEY (`vendor_id`)
              REFERENCES `vendor` (`vendor_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;";
                  
        $this->execute($sql);
        
        $this->execute("update contract_detail cd,
            (select contract.contract_id, contract.vendor_id from contract, contract_detail where contract.contract_id=contract_detail.contract_id)
            cc SET cd.vendor_id=cc.vendor_id WHERE cd.contract_id=cc.contract_id;");
        
    }

    public function down()
    {
        echo "m160721_194639_contract_detail_vendor_id cannot be reverted.\n";

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
