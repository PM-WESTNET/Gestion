<?php

use yii\db\Migration;

class m190830_132525_add_fields_to_payment_plan_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payment_plan', 'created_at', $this->integer()->defaultValue(null));
        $this->addColumn('payment_plan', 'created_by', $this->integer()->defaultValue(null));

        $this->addForeignKey('fk_payment_plan_user_id', 'payment_plan', 'created_by', 'user', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_payment_plan_user_id', 'payment_plan');
        $this->dropColumn('payment_plan', 'created_by');
        $this->dropColumn('payment_plan', 'created_at');
    }
}
