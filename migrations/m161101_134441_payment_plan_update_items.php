<?php

use yii\db\Migration;

class m161101_134441_payment_plan_update_items extends Migration
{
    public function up()
    {
        $this->execute(
            "update product_to_invoice set amount = round(amount/1.21,2) where payment_plan_id is not null and period >= '2016-10-31' and status = 'active'"
        );

    }

    public function down()
    {
        echo "m161101_134441_payment_plan_update_items cannot be reverted.\n";

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
