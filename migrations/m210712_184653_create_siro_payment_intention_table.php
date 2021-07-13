<?php

use yii\db\Migration;

/**
 * Handles the creation of table `siro_payment_intention`.
 */
class m210712_184653_create_siro_payment_intention_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `siro_payment_intention` (
                  `siro_payment_intention_id` INT NOT NULL AUTO_INCREMENT,
                  `customer_id` INT,
                  `hash` TEXT,
                  `reference` TEXT,
                  `url` TEXT,
                  `createdAt` DATETIME,
                  `updatedAt` DATETIME,
                  `status` ENUM('pending', 'canceled', 'payed') NOT NULL DEFAULT 'pending',
                  `id_resultado` TEXT,
                  `id_operacion` TEXT,
                  `estado` TEXT,
                  `fecha_operacion` DATETIME,
                  `fecha_registro` DATETIME,
                  `payment_id` INT,
                  PRIMARY KEY (`siro_payment_intention_id`))
                ENGINE = InnoDB");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('siro_payment_intention');
    }
}
