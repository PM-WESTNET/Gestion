<?php

use yii\db\Migration;

/**
 * Class m201113_204114_customer_has_firstdata_export_table
 */
class m201113_204114_customer_has_firstdata_export_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('bill_has_firstdata_export');

        $this->createTable('customer_has_firstdata_export', [
            'customer_has_firstdata_export_id' => $this->primaryKey(),
            'customer_id' => $this->integer(11),
            'firstdata_export_id' => $this->integer(),
            'month' => $this->string() 
        ]);

        $this->addForeignKey('fk_customer_firstdata_export','customer_has_firstdata_export', 'customer_id', 'customer', 'customer_id');
        $this->addForeignKey('fk_firstdata_export_customer','customer_has_firstdata_export', 'firstdata_export_id', 'firstdata_export', 'firstdata_export_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customer_has_firstdata_export');

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
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201113_204114_customer_has_firstdata_export_table cannot be reverted.\n";

        return false;
    }
    */
}
