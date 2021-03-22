<?php

use yii\db\Migration;

class m161101_185246_geocode_correction extends Migration
{
    public function up()
    {
        $this->execute("UPDATE address SET geocode='-32.8988839,-68.8194614' WHERE geocode='-34.66352, -68.35941'");
    }

    public function down()
    {
        echo "m161101_185246_geocode_correction cannot be reverted.\n";

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
