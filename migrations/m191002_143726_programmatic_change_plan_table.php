<?php

use yii\db\Migration;

/**
 * Class m191002_143726_programmatic_change_plan_table
 */
class m191002_143726_programmatic_change_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `programmatic_change_plan` (
                  `programmatic_change_plan_id` INT NOT NULL AUTO_INCREMENT,
                  `date` INT NOT NULL,
                  `applied` TINYINT(1) NOT NULL DEFAULT 0,
                  `created_at` INT NOT NULL,
                  `updated_at` INT NOT NULL,
                  `contract_id` INT(11) NOT NULL,
                  `product_id` INT(11) NOT NULL,
                  `user_id` INT(11) NOT NULL,
                  PRIMARY KEY (`programmatic_change_plan_id`),
                  INDEX `fk_programmatic_change_plan_contract_idx` (`contract_id` ASC),
                  INDEX `fk_programmatic_change_plan_product1_idx` (`product_id` ASC),
                  INDEX `fk_programmatic_change_plan_user1_idx` (`user_id` ASC),
                  CONSTRAINT `fk_programmatic_change_plan_contract`
                    FOREIGN KEY (`contract_id`)
                    REFERENCES `contract` (`contract_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_programmatic_change_plan_product1`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `product` (`product_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                  CONSTRAINT `fk_programmatic_change_plan_user1`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `user` (`id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('programmatic_change_plan');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191002_143726_programmatic_change_plan_table cannot be reverted.\n";

        return false;
    }
    */
}
