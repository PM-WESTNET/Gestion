<?php

use yii\db\Migration;

class m160914_133230_empty_ads_table extends Migration
{
    public function up()
    {
        $this->execute(
                "CREATE TABLE IF NOT EXISTS `empty_ads` (
                    `empty_ads_id` INT NOT NULL AUTO_INCREMENT,
                    `code` BIGINT NOT NULL,
                    `payment_code` VARCHAR(20) NOT NULL,
                    `node_id` INT NOT NULL,
                    `used` TINYINT(1) NOT NULL DEFAULT 0,
                    `company_id` INT(11) NOT NULL,
                PRIMARY KEY (`empty_ads_id`),
                UNIQUE INDEX `code_UNIQUE` (`code` ASC))
                ENGINE = InnoDB"
                );
    }

    public function down()
    {
        echo "m160914_133230_empty_ads_table cannot be reverted.\n";

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
