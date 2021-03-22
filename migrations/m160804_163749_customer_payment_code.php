<?php

use yii\db\Migration;

class m160804_163749_customer_payment_code extends Migration
{
    public function up()
    {

        $this->execute("ALTER TABLE customer MODIFY payment_code VARCHAR(20) DEFAULT '0';");

    }

    public function down()
    {

        return false;
    }
}
