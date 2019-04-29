<?php

use yii\db\Migration;

/**
 * Class m190325_201249_pagomiscuentas_liquidation
 */
class m190325_201249_pagomiscuentas_liquidation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `pagomiscuentas_liquidation` (
                  `pagomiscuentas_liquidation_id` INT NOT NULL AUTO_INCREMENT,
                  `file` VARCHAR(255) NULL,
                  `created_at` INT NOT NULL,
                  `updated_at` INT NOT NULL,
                  `number` INT NULL,
                  `account_movement_id` INT(11) NULL,
                  PRIMARY KEY (`pagomiscuentas_liquidation_id`),
                  INDEX `fk_pagomiscuentas_liquidation_account_movement_idx` (`account_movement_id` ASC),
                  CONSTRAINT `fk_pagomiscuentas_liquidation_account_movement`
                    FOREIGN KEY (`account_movement_id`)
                    REFERENCES `account_movement` (`account_movement_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('pagomiscuentas_liquidation');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190325_201249_pagomiscuentas_liquidation cannot be reverted.\n";

        return false;
    }
    */
}
