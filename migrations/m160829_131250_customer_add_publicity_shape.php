<?php

use yii\db\Migration;

class m160829_131250_customer_add_publicity_shape extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `customer` 
                        ADD COLUMN `publicity_shape` ENUM('banner', 'poster', 'web', 'other_customer', 
                        'facebook', 'street_banner', 'magazine', 'door_to_door', 'competition') NULL DEFAULT NULL AFTER `payment_code`
                        ");

    }

    public function down()
    {
        echo "m160829_131250_customer_add_publicity_shape cannot be reverted.\n";

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
