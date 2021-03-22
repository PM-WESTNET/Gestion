<?php

use yii\db\Migration;

class m161102_151856_tax_condition_has_document_type extends Migration
{
    public function up()
    {
            
        
        $this->execute("
            CREATE TABLE IF NOT EXISTS `tax_condition_has_document_type` (
                `tax_condition_id` INT(11) NOT NULL,
                `document_type_document_type_id` INT(11) NOT NULL,
            PRIMARY KEY (`tax_condition_id`, `document_type_document_type_id`),
            INDEX `fk_tax_condition_has_document_type_document_type1_idx` (`document_type_document_type_id` ASC),
            INDEX `fk_tax_condition_has_document_type_tax_condition1_idx` (`tax_condition_id` ASC),
            CONSTRAINT `fk_tax_condition_has_document_type_tax_condition1`
                FOREIGN KEY (`tax_condition_id`)
                REFERENCES `tax_condition` (`tax_condition_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `fk_tax_condition_has_document_type_document_type1`
                FOREIGN KEY (`document_type_document_type_id`)
                REFERENCES `document_type` (`document_type_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8");
        
        $tax_conditions= $this->db->createCommand("SELECT tax_condition_id, document_type_id FROM tax_condition")->queryAll();
        
        foreach ($tax_conditions as $tax) {
            $this->insert('tax_condition_has_document_type', ['tax_condition_id' => $tax['tax_condition_id'], 'document_type_document_type_id' => $tax['document_type_id']]);
        }
        
        $this->execute(
                " ALTER TABLE `tax_condition` 
                    DROP FOREIGN KEY `fk_tax_condition_document_type1`
                    ");
        
        $this->execute("ALTER TABLE `tax_condition` 
                    DROP COLUMN `document_type_id`,
                    DROP INDEX `fk_tax_condition_document_type1_idx`");
        
        
    }

    public function down()
    {
        echo "m161102_151856_tax_condition_has_document_type cannot be reverted.\n";

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
