<?php

use yii\db\Migration;

class m161017_173825_brochure_publicity_shape extends Migration
{
    public function up()
    {
        $this->execute(
                "ALTER TABLE `customer`
                 CHANGE COLUMN `publicity_shape` `publicity_shape` ENUM('banner', 
                 'poster', 'web', 'other_customer', 'facebook', 'street_banner',
                 'magazine', 'door_to_door', 'competition', 'brochure') NULL DEFAULT NULL ");
    }

    public function down()
    {
        echo "m161017_173825_brochure_publicity_shape cannot be reverted.\n";

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
